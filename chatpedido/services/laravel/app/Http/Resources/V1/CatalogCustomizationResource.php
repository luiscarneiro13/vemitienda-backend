<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogCustomizationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'template_id' => $this->resource['template_id'] ?? 'grid_2col',
            'primary_color' => $this->resource['primary_color'] ?? '#6135bc',
            'secondary_color' => $this->resource['secondary_color'] ?? '#ff6b00',
            'show_search' => (bool) ($this->resource['show_search'] ?? true),
            'show_delivery_time' => (bool) ($this->resource['show_delivery_time'] ?? true),
            'delivery_time_text' => $this->resource['delivery_time_text'] ?? '15-25 min',
            'show_free_shipping_bar' => (bool) ($this->resource['show_free_shipping_bar'] ?? false),
            'free_shipping_threshold' => (float) ($this->resource['free_shipping_threshold'] ?? 20.0),
            'show_promo_banner' => (bool) ($this->resource['show_promo_banner'] ?? false),
            'promo_title' => $this->resource['promo_title'] ?? '',
            'promo_subtitle' => $this->resource['promo_subtitle'] ?? '',
            'promo_code' => $this->resource['promo_code'] ?? '',
            'show_featured_carousel' => (bool) ($this->resource['show_featured_carousel'] ?? true),
            'preview_url' => $this->resource['preview_url'] ?? null,
        ];
    }
}
