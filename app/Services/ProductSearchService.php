<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearchService
{
    public function search(array $params, int $limit = 8)
    {
        $q         = trim((string)($params['q'] ?? ''));
        $brand     = $params['brand'] ?? null;
        $category  = $params['category'] ?? null;
        $priceMin  = isset($params['price_min']) ? (float)$params['price_min'] : null;
        $priceMax  = isset($params['price_max']) ? (float)$params['price_max'] : null;

        if ($q === '' && !$brand && !$category && $priceMin === null && $priceMax === null) {
            return collect();
        }

        $res = $this->runQuery($brand, $category, $priceMin, $priceMax, $q, $limit);
        if ($res->count()) return $res;

        if ($brand && $category) {
            $res = $this->runQuery($brand, null, $priceMin, $priceMax, $q, $limit);
            if ($res->count()) return $res;
        }

        $res = $this->runQuery($brand, $category, $priceMin, $priceMax, '', $limit);
        if ($res->count()) return $res;

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

        if ($brand)    $query->where('brand', $brand);
        if ($category) $query->where('category', $category);
        if ($priceMin !== null) $query->where('price', '>=', $priceMin);
        if ($priceMax !== null) $query->where('price', '<=', $priceMax);

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

        $text = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

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

        $raw = preg_split('/[^\p{L}\p{N}\.]+/u', (string)$lower, -1, PREG_SPLIT_NO_EMPTY);

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

        if ($low->contains(['điện thoại', 'tai nghe', 'loa', 'điện tử', 'headphone', 'speaker'])) {
            $out['category'] = 'Electronics';
        }
        if ($low->contains(['nồi', 'bếp', 'nhà bếp', 'airfryer', 'máy pha'])) {
            $out['category'] = 'Kitchen';
        }
        if ($low->contains(['sách', 'book', 'ui', 'php', 'mysql', 'system design', 'algorithm', 'thuật toán'])) {
            $out['category'] = 'Books';
        }

        $textNorm = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

        if (preg_match('/(\d+)\s*-\s*(\d+)/', $textNorm, $m)) {
            $a = (float)$m[1];
            $b = (float)$m[2];
            $out['price_min'] = min($a, $b);
            $out['price_max'] = max($a, $b);
        } elseif (preg_match('/(under|dưới|<=)\s*(\d+)/i', $textNorm, $m)) {
            $out['price_max'] = (float)$m[2];
        } elseif (preg_match('/(>=|trên|tối thiểu|at least)\s*(\d+)/i', $textNorm, $m)) {
            $out['price_min'] = (float)$m[2];
        }

        return $out;
    }
}
