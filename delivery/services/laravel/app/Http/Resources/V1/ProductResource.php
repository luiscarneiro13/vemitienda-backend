<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Domain\Catalog\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'company_id' => $this->company_id,
            'catalog_id' => $this->catalog_id,
            'catalog_category_id' => $this->catalog_category_id,
            'category' => new CatalogCategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'price_formatted' => number_format((float) $this->price, 2),
            'compare_at_price' => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
            'badge' => $this->badge,
            'image_url' => $this->imageUrl(),
            'has_uploaded_image' => filled($this->image_path),
            'is_active' => (bool) $this->is_active,
            'is_available' => (bool) $this->is_available,
            'is_featured' => (bool) $this->is_featured,
            'sort_order' => (int) $this->sort_order,
            'option_groups_count' => $this->whenCounted('optionGroups'),
            'option_groups' => ProductOptionGroupResource::collection($this->whenLoaded('optionGroups')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
