<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GroqClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config(
            'services.groq.base_url',
            env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1')
        );
        $this->apiKey  = (string) env('GROQ_API_KEY', '');
        $this->model   = (string) env('GROQ_MODEL', 'meta-llama/llama-3.1-70b-instruct');
    }

    public function chat(array $messages, array $opts = []): array
    {
        $payload = array_filter([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $opts['temperature'] ?? 0.2,
            'max_tokens' => $opts['max_tokens'] ?? 800,
            'top_p' => $opts['top_p'] ?? 1.0,
            'stream' => false,
        ]);

        $resp = Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->asJson()
            ->post('/chat/completions', $payload)
            ->throw();

        $data = $resp->json();

        $content = Arr::get($data, 'choices.0.message.content', '');
        return [
            'content' => $content,
            'raw' => $data,
        ];
    }

    public function buildMessages(string $userText, array $productBullets): array
    {
        $system = <<<SYS
            Bạn là AI Chat Support cho trang thương mại điện tử.

            Quy tắc:
            - Chỉ giới thiệu sản phẩm CÓ trong danh sách ngữ cảnh.
            - Mỗi đề xuất PHẢI kèm "Link: <url>" đúng như context.
            - Viết NGẮN GỌN, TIẾNG VIỆT tự nhiên.
            - **KHÔNG dùng Markdown, KHÔNG dùng ký tự in đậm/italic, KHÔNG dùng dấu *, #, hoặc code block.**
            - Dùng gạch đầu dòng bằng "- " đơn giản (không **, không __).
            SYS;


        $context = "Context (sản phẩm khớp tìm kiếm):\n" . implode("\n", $productBullets);

        return [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $context . "\n\nYêu cầu: " . $userText],
        ];
    }
}
