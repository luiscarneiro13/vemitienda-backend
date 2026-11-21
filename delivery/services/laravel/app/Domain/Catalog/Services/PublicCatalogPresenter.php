<?php

namespace App\Domain\Catalog\Services;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanySettingsManager;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use Illuminate\Support\Facades\Cache;

class PublicCatalogPresenter
{
    private CompanySettingsManager $settingsManager;

    public function __construct(?CompanySettingsManager $settingsManager = null)
    {
        $this->settingsManager = $settingsManager ?? app(CompanySettingsManager::class);
    }

    public function present(Company $company, Branch $branch, Catalog $catalog): array
    {
        $version = PublicCatalogCache::companyVersion((int) $company->id);
        $cacheKey = "public_catalog:payload:{$company->id}:{$branch->id}:{$catalog->id}:v{$version}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($company, $branch, $catalog): array {
            return $this->buildPayload($company, $branch, $catalog);
        });
    }

    private function buildPayload(Company $company, Branch $branch, Catalog $catalog): array
    {
        $branch->loadMissing('cityRecord:id,name');

        $categories = CatalogCategory::withoutTenancy()
            ->where('catalog_id', $catalog->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['products' => fn ($q) => $q->withoutTenancy()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->with(['optionGroups' => fn ($q) => $q->withoutTenancy()
                    ->orderBy('sort_order')
                    ->with(['options' => fn ($q) => $q->withoutTenancy()
                        ->where('is_active', true)
                        ->orderBy('sort_order')])])])
            ->get()
            ->filter(fn (CatalogCategory $category): bool => $category->products->isNotEmpty())
            ->values();

        $settings = $this->resolveSettings($company);

        $allProducts = $categories->flatMap(fn (CatalogCategory $c) => $c->products);
        $featuredProducts = $allProducts
            ->filter(fn (Product $p): bool => (bool) $p->is_featured && $p->is_available)
            ->values()
            ->map(fn (Product $p): array => $this->formatProduct($p))
            ->all();

        return [
            'store' => [
                'name' => $company->name,
                'logo_url' => $company->logoUrl(),
                'initials' => $company->initials(),
                'currency' => $company->currency ?: 'USD',
                'tax_id' => $company->tax_id,
                'address' => $company->address,
                'description' => $company->store_description,
            ],
            'branch' => [
                'uuid' => $branch->uuid,
                'name' => $branch->name,
                'city' => $branch->cityRecord?->name,
                'whatsapp' => $branch->whatsapp_number,
            ],
            'settings' => $settings,
            'featured_products' => $featuredProducts,
            'track_order_url' => route('public.catalog.track_order', [
                'companySlug' => $company->slug,
                'branchUuid' => $branch->uuid,
            ]),
            'categories' => $categories->map(fn (CatalogCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'description' => $category->description,
                'products' => $category->products->map(fn (Product $product): array => $this->formatProduct($product))->values()->all(),
            ])->all(),
        ];
    }

    private function resolveSettings(Company $company): array
    {
        $group = $this->settingsManager->group($company, 'catalog');

        return [
            'template_id' => $group['catalog.template_id'] ?? $group['template_id'] ?? 'grid_2col',
            'primary_color' => $group['catalog.primary_color'] ?? $group['primary_color'] ?? '#a04100',
            'secondary_color' => $group['catalog.secondary_color'] ?? $group['secondary_color'] ?? '#b7131a',
            'show_search' => (bool) ($group['catalog.show_search'] ?? $group['show_search'] ?? true),
            'show_delivery_time' => (bool) ($group['catalog.show_delivery_time'] ?? $group['show_delivery_time'] ?? true),
            'delivery_time_text' => (string) ($group['catalog.delivery_time_text'] ?? $group['delivery_time_text'] ?? '15-25 min'),
            'show_free_shipping_bar' => (bool) ($group['catalog.show_free_shipping_bar'] ?? $group['show_free_shipping_bar'] ?? false),
            'free_shipping_threshold' => (float) ($group['catalog.free_shipping_threshold'] ?? $group['free_shipping_threshold'] ?? 20.00),
            'show_promo_banner' => (bool) ($group['catalog.show_promo_banner'] ?? $group['show_promo_banner'] ?? false),
            'promo_title' => (string) ($group['catalog.promo_title'] ?? $group['promo_title'] ?? ''),
            'promo_subtitle' => (string) ($group['catalog.promo_subtitle'] ?? $group['promo_subtitle'] ?? ''),
            'promo_code' => (string) ($group['catalog.promo_code'] ?? $group['promo_code'] ?? ''),
            'show_featured_carousel' => (bool) ($group['catalog.show_featured_carousel'] ?? $group['show_featured_carousel'] ?? true),
        ];
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'uuid' => $product->uuid,
            'name' => $product->name,
            'description' => $product->description,
            'price_cents' => $this->cents($product->price),
            'compare_at_price_cents' => $product->compare_at_price ? $this->cents($product->compare_at_price) : null,
            'badge' => $product->badge,
            'is_featured' => (bool) $product->is_featured,
            'image_url' => $product->imageUrl(),
            'is_available' => $product->is_available,
            'option_groups' => $product->optionGroups
                ->filter(fn (ProductOptionGroup $group): bool => $group->options->isNotEmpty())
                ->map(fn (ProductOptionGroup $group): array => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'selection_type' => $group->selection_type,
                    'is_required' => $group->is_required,
                    'max_selections' => $group->max_selections,
                    'is_removal' => $group->is_removal,
                    'options' => $group->options->map(fn (ProductOption $option): array => [
                        'id' => $option->id,
                        'name' => $option->name,
                        'price_cents' => $this->cents($option->price),
                    ])->values()->all(),
                ])->values()->all(),
        ];
    }

    private function cents(string|int|float|null $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
