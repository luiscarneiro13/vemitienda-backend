<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Domain\Catalog\Models\ProductOptionGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductOptionGroup
 */
class ProductOptionGroupResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'selection_type' => $this->selection_type instanceof \BackedEnum ? $this->selection_type->value : (string) $this->selection_type,
            'is_required' => (bool) $this->is_required,
            'max_selections' => $this->max_selections !== null ? (int) $this->max_selections : null,
            'is_removal' => (bool) $this->is_removal,
            'sort_order' => (int) $this->sort_order,
            'options_count' => $this->whenCounted('options'),
            'options' => ProductOptionResource::collection($this->whenLoaded('options')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
