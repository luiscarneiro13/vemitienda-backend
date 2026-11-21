<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Domain\Catalog\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductOption
 */
class ProductOptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_option_group_id' => $this->product_option_group_id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'price_formatted' => number_format((float) $this->price, 2),
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
