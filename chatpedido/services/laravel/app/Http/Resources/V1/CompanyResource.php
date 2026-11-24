<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Core\Companies\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'legal_name' => $this->legal_name,
            'tax_id' => $this->tax_id,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo,
            'logo_url' => $this->logoUrl(),
            'store_category' => $this->store_category,
            'store_description' => $this->store_description,
            'cover_image_url' => $this->coverImageUrl(),
            'currency' => $this->currency ?? 'USD',
            'timezone' => $this->timezone ?? 'America/Caracas',
            'status' => $this->status ?? 'active',
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
