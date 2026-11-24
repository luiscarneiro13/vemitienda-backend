<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Domain\Catalog\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Catalog
 */
class CatalogResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => (bool) $this->is_default,
            'is_active' => (bool) $this->is_active,
            'categories_count' => $this->whenCounted('categories'),
            'products_count' => $this->whenCounted('products'),
            'categories' => CatalogCategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
