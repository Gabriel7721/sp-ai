<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatSendRequest;
use App\Http\Resources\ProductResource;
use App\Models\Chat;
use App\Models\Message;
use App\Services\GroqClient;
use App\Services\ProductSearchService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatController extends Controller
{
    public function send(ChatSendRequest $request, ProductSearchService $search, GroqClient $groq)
    {
        $user = $request->user();
        $text = trim($request->input('text', ''));
        $chatId = $request->input('chat_id');

        $chat = $chatId
            ? Chat::where('user_id', $user->id)->findOrFail($chatId)
            : Chat::create(['user_id' => $user->id, 'title' => null]);

        if ($this->isGreeting($text)) {
            $reply = "Chào bạn! Mình có thể giúp tìm sản phẩm theo nhãn hiệu, danh mục hoặc tầm giá.\nVí dụ:\n- “tai nghe chống ồn Sony tầm 300–400”\n- “nồi chiên không dầu Philips dưới 200”\n- “sách Laravel cho người mới”";
            Message::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $text,
                'meta' => null,
            ]);
            Message::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => $reply,
                'meta' => ['products' => []],
            ]);
            return response()->json([
                'chat_id' => $chat->id,
                'reply' => $reply,
                'matched_products' => [],
            ]);
        }

        $filters = $search->guessFiltersFromText($text);
        try {
            $products = $search->search(array_merge(['q' => $text], $filters), 8);
        } catch (QueryException $qe) {
            Log::warning('FULLTEXT failed, fallback to LIKE', ['err' => $qe->getMessage()]);
            $products = $search->search(['q' => $text, ...$filters, '__force_like' => true], 8);
        }

        $bullets  = $search->toPromptBullets($products);

        $reply = null;
        $aiRaw = null;

        try {
            $messages = $groq->buildMessages($text, $bullets);
            $ai = $groq->chat($messages, [
                'temperature' => 0.2,
                'max_tokens' => 700,
            ]);
            $reply = (string) $ai['content'];
            $aiRaw = $ai['raw'] ?? null;
        } catch (Throwable $e) {
            Log::error('Groq call failed', ['err' => $e->getMessage()]);
            if ($products->count()) {
                $lines = $products->map(
                    fn($p) =>
                    "- {$p->name} ({$p->brand}, {$p->category}) — {$p->price} {$p->currency}\n  Link: {$p->url}"
                )->implode("\n");
                $reply = "Mình chưa gọi được AI, nhưng có vài sản phẩm phù hợp:\n{$lines}";
            } else {
                $reply = "Mình chưa gọi được AI và hiện chưa tìm thấy sản phẩm phù hợp. Bạn thử mô tả lại (nhãn hiệu, danh mục, tầm giá)…";
            }
        }

        DB::transaction(function () use ($chat, $text, $reply, $products, $filters, $aiRaw) {
            Message::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $text,
                'meta' => ['filters' => $filters],
            ]);

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => $reply,
                'meta' => [
                    'products' => $products->take(8)->values()->toArray(),
                    'ai_raw'   => $aiRaw,
                ],
            ]);
        });

        if (app()->isLocal()) {
            Log::debug('SEARCH_DEBUG', [
                'text' => $text,
                'filters' => $filters,
                'count' => $products->count(),
                'ids' => $products->pluck('id'),
            ]);
        }
        return response()->json([
            'chat_id' => $chat->id,
            'reply' => $reply,
            'matched_products' => $products,
        ]);
    }

    protected function isGreeting(string $text): bool
    {
        $t = mb_strtolower(trim($text));
        $greetings = [
            'hi',
            'hello',
            'hey',
            'chào',
            'xin chào',
            'chao',
            'alo',
            'yo',
            'hola',
            'sup',
            'hi!',
            'hello!',
            'chào bạn',
            'chào ad',
            'chào shop',
            'good morning',
            'good afternoon',
            'good evening'
        ];
        foreach ($greetings as $g) {
            if ($t === $g || str_starts_with($t, $g . ' ')) return true;
        }
        if (str_word_count($t) <= 3 && !preg_match('/\d+|tai nghe|headphone|loa|sách|book|iphone|samsung|sony|philips|breville|zojirushi|lodge/i', $t)) {
            return true;
        }
        return false;
    }
}
