<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreMetricsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'currency' => $this->resource['currency'] ?? 'USD',
            'total_orders' => (int) ($this->resource['total_orders'] ?? 0),
            'total_views' => (int) ($this->resource['total_views'] ?? 0),
            'total_cents' => (int) ($this->resource['total_cents'] ?? 0),
            'estimated_sales' => round(((int) ($this->resource['total_cents'] ?? 0)) / 100, 2),
            'conversion_rate' => (float) ($this->resource['conversion_rate'] ?? 0.0),
            'average_ticket' => (float) ($this->resource['average_ticket'] ?? 0.0),
            'last_order_at' => $this->resource['last_order_at'] ?? null,
            'products_count' => (int) ($this->resource['products_count'] ?? 0),
            'active_products' => (int) ($this->resource['active_products'] ?? 0),
            'public_catalog_url' => $this->resource['public_catalog_url'] ?? null,
        ];
    }
}
