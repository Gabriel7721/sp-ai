<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'brand',
        'category',
        'description',
        'price',
        'currency',
        'stock',
        'url',
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
    ];

    public function scopeFulltext($q, string $term)
    {
        return $q->whereRaw(
            "MATCH(name, brand, category, description) AGAINST (? IN NATURAL LANGUAGE MODE)",
            [$term]
        );
    }
}
