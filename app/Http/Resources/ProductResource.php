<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'brand'    => $this->brand,
            'category' => $this->category,
            'price'    => (float) $this->price,
            'url'      => $this->url,
            'image'    => $this->image_url ?? null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
