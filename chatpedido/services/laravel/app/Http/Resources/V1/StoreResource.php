<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Core\Companies\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class StoreResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $branch = $this->branches()->withoutTenancy()->with('cityRecord')->first();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'legal_name' => $this->legal_name,
            'tax_id' => $this->tax_id,
            'address' => $this->address ?? $branch?->address,
            'email' => $this->email ?? $branch?->email,
            'phone' => $this->phone ?? $branch?->phone,
            'logo' => $this->logo,
            'logo_url' => $this->logoUrl(),
            'store_category' => $this->store_category,
            'store_description' => $this->store_description,
            'cover_image_url' => $this->coverImageUrl(),
            'currency' => $this->currency ?? 'USD',
            'timezone' => $this->timezone ?? 'America/Caracas',
            'status' => $this->status ?? 'active',
            'is_active' => (bool) $this->is_active,
            'public_catalog_url' => $branch?->publicCatalogUrl(),
            'branch' => $branch ? new BranchResource($branch) : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
