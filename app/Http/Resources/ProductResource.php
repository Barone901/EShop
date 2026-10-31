<?php

// [AI] Vygenerovane s pomocou AI (Claude) - presunute z ProductService::formatForApi().

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Produkt vo vysledkoch vyhladavania (autocomplete v hlavicke).
 *
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'image_url' => $this->image_src,
            'category' => $this->category?->name,
            'in_stock' => $this->is_in_stock,
            'url' => route('product.show', $this->resource),
        ];
    }
}
