<?php

// [AI] Vygenerovane s pomocou AI (Claude) - presunute z ProductService::formatDetailForApi().
// [AI] Upravene s pomocou AI (Claude): pole low_stock pre quick view.

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Detail produktu pre quick view.
 *
 * @mixin \App\Models\Product
 */
class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'stock' => $this->stock,
            'in_stock' => $this->is_in_stock,
            'low_stock' => $this->is_low_stock,
            'image_url' => $this->image_src,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'url' => route('product.show', $this->resource),
        ];
    }
}
