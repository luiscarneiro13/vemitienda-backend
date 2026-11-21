<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Domain\Catalog\Models\CatalogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CatalogCategory
 */
class CatalogCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'catalog_id' => $this->catalog_id,
            'name' => $this->name,
            'icon' => $this->icon,
            'description' => $this->description,
            'sort_order' => (int) $this->sort_order,
            'is_active' => (bool) $this->is_active,
            'products_count' => $this->whenCounted('products'),
            'can_delete' => ($this->products_count ?? $this->products()->withoutTenancy()->count()) === 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
