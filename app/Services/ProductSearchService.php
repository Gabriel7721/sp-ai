<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * Tìm kiếm “thực dụng” ưu tiên filter trước, rồi mới áp q theo token.
     * Chiến lược:
     *  - Áp brand/category/price trước.
     *  - Nếu có q: tách token (kể cả tiếng Việt), AND các token qua (name|brand|category|description) LIKE.
     *  - Nếu không thấy kết quả: thử bỏ q, chỉ giữ filters (brand/category/price).
     *  - Nếu vẫn không thấy: nới lỏng price ±10%.
     *  - Nếu vẫn không có: trả rỗng.
     */
    public function search(array $params, int $limit = 8)
    {
        $q         = trim((string)($params['q'] ?? ''));
        $brand     = $params['brand'] ?? null;
        $category  = $params['category'] ?? null;
        $priceMin  = isset($params['price_min']) ? (float)$params['price_min'] : null;
        $priceMax  = isset($params['price_max']) ? (float)$params['price_max'] : null;

        // ⛔ Không có q và không filter → trả rỗng (collect())
        if ($q === '' && !$brand && !$category && $priceMin === null && $priceMax === null) {
            return collect();
        }

        // Pass 1
        $res = $this->runQuery($brand, $category, $priceMin, $priceMax, $q, $limit);
        if ($res->count()) return $res;

        // NEW: nếu có cả brand & category mà rỗng -> thử lại bỏ category
        if ($brand && $category) {
            $res = $this->runQuery($brand, null, $priceMin, $priceMax, $q, $limit);
            if ($res->count()) return $res;
        }

        // Pass 2: chỉ filters (giữ nguyên brand/category hiện có)
        $res = $this->runQuery($brand, $category, $priceMin, $priceMax, '', $limit);
        if ($res->count()) return $res;

        // Pass 3: nới giá
        if ($priceMin !== null || $priceMax !== null) {
            $span = ($priceMin !== null && $priceMax !== null)
                ? max(10.0, 0.1 * ($priceMax - $priceMin))
                : 50.0;
            $res = $this->runQuery(
                $brand,
                $category,
                $priceMin !== null ? max(0, $priceMin - $span) : null,
                $priceMax !== null ? $priceMax + $span : null,
                '',
                $limit
            );
            if ($res->count()) return $res;
        }

        return $res;
    }


    protected function runQuery(?string $brand, ?string $category, ?float $priceMin, ?float $priceMax, string $q, int $limit)
    {
        $query = Product::query();

        // 1) Áp filter cứng
        if ($brand)    $query->where('brand', $brand);
        if ($category) $query->where('category', $category);
        if ($priceMin !== null) $query->where('price', '>=', $priceMin);
        if ($priceMax !== null) $query->where('price', '<=', $priceMax);

        // 2) Áp q theo token (AND giữa các token, OR qua các cột)
        $tokens = $this->tokenize($q);
        if (!empty($tokens)) {
            $query->where(function (Builder $b) use ($tokens) {
                foreach ($tokens as $token) {
                    $b->where(function (Builder $c) use ($token) {
                        $like = '%' . $token . '%';
                        $c->where('name', 'like', $like)
                            ->orWhere('brand', 'like', $like)
                            ->orWhere('category', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
                }
            });
        }

        // Sắp xếp đơn giản: ưu tiên brand/category rồi giá
        $query->orderByRaw('brand ASC, category ASC, price ASC');

        return $query->limit($limit)->get([
            'id',
            'sku',
            'name',
            'brand',
            'category',
            'price',
            'currency',
            'url',
            'attributes',
            'description'
        ]);
    }

    protected function tokenize(string $text): array
    {
        if ($text === '') return [];

        // Chuẩn hóa dấu nối: convert en dash/em dash thành hyphen
        $text = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

        // Xóa các cụm hay gây nhiễu (stop phrases) cơ bản tiếng Việt/Anh
        $noise = [
            'tôi cần',
            'mình cần',
            'cần',
            'muốn',
            'tìm',
            'nhãn hiệu',
            'hãng',
            'thương hiệu',
            'tầm',
            'khoảng',
            'giá',
            'dưới',
            'trên',
            'hoặc',
            'và',
            'theo',
            'mới',
            'cho',
            'của',
            'a',
            'an',
            'the',
            'about',
            'around',
            'under',
            'over',
            'between',
        ];
        $lower = Str::of($text)->lower();
        foreach ($noise as $n) {
            $lower = $lower->replace($n, ' ');
        }

        // Tách token theo ký tự không phải chữ/số (giữ unicode)
        $raw = preg_split('/[^\p{L}\p{N}\.]+/u', (string)$lower, -1, PREG_SPLIT_NO_EMPTY);

        // Loại token quá ngắn/bất lợi
        $tokens = array_values(array_filter($raw, fn($t) => Str::length($t) >= 2));

        return $tokens;
    }

    public function toPromptBullets($products): array
    {
        return $products->map(function (Product $p) {
            $attrs = [];
            foreach ((array)$p->attributes as $k => $v) {
                $attrs[] = "{$k}: {$v}";
            }
            $attrStr = $attrs ? ' | ' . implode(', ', $attrs) : '';
            return "- {$p->name} ({$p->brand}, {$p->category}) — {$p->price} {$p->currency}{$attrStr} — Link: {$p->url}";
        })->all();
    }

    /**
     * Đoán filters từ câu người dùng (mở rộng): brand, category, price range.
     * Hỗ trợ en dash `–`, khoảng "300-400", "300 – 400", "<= 200", "under 200".
     */
    public function guessFiltersFromText(string $text): array
    {
        $out = [];

        $brands = ['Apple', 'Samsung', 'Sony', 'Google', 'Philips', 'Breville', 'Zojirushi', 'Lodge', 'Anker', "O'Reilly", 'Pearson', 'Manning', 'Independent', 'php[architect]'];
        $low = Str::of($text)->lower();

        foreach ($brands as $b) {
            if ($low->contains(Str::lower($b))) {
                $out['brand'] = $b;
                break;
            }
        }

        // Category bằng từ khóa Việt
        if ($low->contains(['điện thoại', 'tai nghe', 'loa', 'điện tử', 'headphone', 'speaker'])) {
            $out['category'] = 'Electronics';
        }
        if ($low->contains(['nồi', 'bếp', 'nhà bếp', 'airfryer', 'máy pha'])) {
            $out['category'] = 'Kitchen';
        }
        if ($low->contains(['sách', 'book', 'ui', 'php', 'mysql', 'system design', 'algorithm', 'thuật toán'])) {
            $out['category'] = 'Books';
        }

        // Chuẩn hóa dấu nối
        $textNorm = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

        // khoảng giá "300-400"
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $textNorm, $m)) {
            $a = (float)$m[1];
            $b = (float)$m[2];
            $out['price_min'] = min($a, $b);
            $out['price_max'] = max($a, $b);
        }
        // dưới/under
        elseif (preg_match('/(under|dưới|<=)\s*(\d+)/i', $textNorm, $m)) {
            $out['price_max'] = (float)$m[2];
        }
        // trên/ít nhất
        elseif (preg_match('/(>=|trên|tối thiểu|at least)\s*(\d+)/i', $textNorm, $m)) {
            $out['price_min'] = (float)$m[2];
        }

        return $out;
    }
}
