<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Core\Branches\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Branch
 */
class BranchResource extends JsonResource
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
            'whatsapp_number' => $this->whatsapp_number,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->whenLoaded('cityRecord')),
            'status' => $this->status ?? 'active',
            'is_main' => (bool) $this->is_main,
            'is_publicly_available' => $this->isPubliclyAvailable(),
            'public_catalog_url' => $this->publicCatalogUrl(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
