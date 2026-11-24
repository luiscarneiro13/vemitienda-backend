<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanySettingsManager;
use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogCustomizationResource;
use App\Http\Resources\V1\CatalogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CatalogCustomizationController extends Controller
{
    /**
     * Consultar metadatos del catálogo único de la tienda activa.
     */
    public function getCatalog(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('view', $company);

        $catalog = $company->defaultCatalog()->withoutTenancy()->firstOrFail();
        Gate::authorize('view', $catalog);

        return response()->json([
            'data' => new CatalogResource($catalog),
        ]);
    }

    /**
     * Actualizar estado y metadatos del catálogo único (publicar/pausar).
     */
    public function updateCatalog(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        $catalog = $company->defaultCatalog()->withoutTenancy()->firstOrFail();
        Gate::authorize('update', $catalog);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $catalog->update($validated);

        return response()->json([
            'message' => 'Catálogo actualizado exitosamente.',
            'data' => new CatalogResource($catalog->fresh()),
        ]);
    }

    /**
     * Listar las plantillas visuales disponibles para el catálogo web.
     */
    public function templates(): JsonResponse
    {
        $templates = [
            [
                'id' => 'grid_2col',
                'name' => 'Cuadrícula 2x2 (Grid Visual)',
                'description' => '2 columnas con fotos amplias 4:3, badges de descuento y chips flotantes de categoría.',
                'badge' => 'Recomendada',
                'image_url' => asset('images/templates/grid_2col.png'),
            ],
            [
                'id' => 'compact_list',
                'name' => 'Lista Compacta (Menú Express)',
                'description' => 'Filas horizontales ultrarrápidas pensadas para negocios con alta rotación o muchas opciones.',
                'badge' => 'Express',
                'image_url' => asset('images/templates/compact_list.png'),
            ],
            [
                'id' => 'editorial_cards',
                'name' => 'Tarjetas Editoriales (Gourmet)',
                'description' => 'Tarjetas grandes 1 columna con tipografía destacada e ingredientes en primer plano.',
                'badge' => 'Gourmet',
                'image_url' => asset('images/templates/editorial_cards.png'),
            ],
            [
                'id' => 'inline_selector',
                'name' => 'Selector en Línea (Fast Order)',
                'description' => 'Cantidades y extras ajustables directamente en la tarjeta principal.',
                'badge' => 'Rápido',
                'image_url' => asset('images/templates/inline_selector.png'),
            ],
            [
                'id' => 'flash_deals',
                'name' => 'Ofertas y Promociones',
                'description' => 'Precios tachados en grande, porcentajes de descuento y badges destacados.',
                'badge' => 'Promociones',
                'image_url' => asset('images/templates/flash_deals.png'),
            ],
            [
                'id' => 'dark_midnight',
                'name' => 'Midnight Dark (Elegante Oscuro)',
                'description' => 'Tema oscuro inmersivo ideal para hamburgueserías gourmet, bares y dark kitchens nocturnas.',
                'badge' => 'Modo Oscuro',
                'image_url' => asset('images/templates/dark_midnight.png'),
            ],
            [
                'id' => 'combo_wizard',
                'name' => 'Combos y Menús del Día',
                'description' => 'Destaca paquetes familiares y promociones especiales.',
                'badge' => 'Combos',
                'image_url' => asset('images/templates/combo_wizard.png'),
            ],
            [
                'id' => 'family_combos',
                'name' => 'Promociones Familiares',
                'description' => 'Diseño enfocado en platos para compartir y grandes combos.',
                'badge' => 'Familiar',
                'image_url' => asset('images/templates/family_combos.png'),
            ],
            [
                'id' => 'brand_directory',
                'name' => 'Directorio de Marca',
                'description' => 'Presentación institucional con banner de marca y catálogo sobrio.',
                'badge' => 'Institucional',
                'image_url' => asset('images/templates/brand_directory.png'),
            ],
            [
                'id' => 'healthy_macros',
                'name' => 'Saludable & Fresco',
                'description' => 'Acentos frescos, badges de ingredientes y diseño ligero.',
                'badge' => 'Healthy',
                'image_url' => asset('images/templates/healthy_macros.png'),
            ],
        ];

        return response()->json([
            'data' => $templates,
        ]);
    }

    /**
     * Consultar la configuración visual actual del catálogo.
     */
    public function getCustomization(): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('view', $company);

        $settingsManager = app(CompanySettingsManager::class);
        $settings = $settingsManager->group($company, 'catalog');
        $branch = $company->branches()->withoutTenancy()->first();

        $data = [
            'template_id' => $settings['template_id'] ?? 'grid_2col',
            'primary_color' => $settings['primary_color'] ?? '#6135bc',
            'secondary_color' => $settings['secondary_color'] ?? '#ff6b00',
            'show_search' => (bool) ($settings['show_search'] ?? true),
            'show_delivery_time' => (bool) ($settings['show_delivery_time'] ?? true),
            'delivery_time_text' => $settings['delivery_time_text'] ?? '15-25 min',
            'show_free_shipping_bar' => (bool) ($settings['show_free_shipping_bar'] ?? false),
            'free_shipping_threshold' => (float) ($settings['free_shipping_threshold'] ?? 20.0),
            'show_promo_banner' => (bool) ($settings['show_promo_banner'] ?? false),
            'promo_title' => $settings['promo_title'] ?? '',
            'promo_subtitle' => $settings['promo_subtitle'] ?? '',
            'promo_code' => $settings['promo_code'] ?? '',
            'show_featured_carousel' => (bool) ($settings['show_featured_carousel'] ?? true),
            'preview_url' => $branch?->publicCatalogUrl(),
        ];

        return response()->json([
            'data' => new CatalogCustomizationResource($data),
        ]);
    }

    /**
     * Actualizar la configuración visual del catálogo.
     */
    public function updateCustomization(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        $validated = $request->validate([
            'template_id' => [
                'sometimes', 'required', 'string',
                Rule::in([
                    'grid_2col', 'compact_list', 'editorial_cards', 'inline_selector',
                    'flash_deals', 'dark_midnight', 'combo_wizard', 'family_combos',
                    'brand_directory', 'healthy_macros',
                ]),
            ],
            'primary_color' => ['sometimes', 'required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['sometimes', 'required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'show_search' => ['sometimes', 'boolean'],
            'show_delivery_time' => ['sometimes', 'boolean'],
            'delivery_time_text' => ['sometimes', 'nullable', 'string', 'max:50'],
            'show_free_shipping_bar' => ['sometimes', 'boolean'],
            'free_shipping_threshold' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'show_promo_banner' => ['sometimes', 'boolean'],
            'promo_title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'promo_subtitle' => ['sometimes', 'nullable', 'string', 'max:200'],
            'promo_code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'show_featured_carousel' => ['sometimes', 'boolean'],
        ]);

        $types = [
            'template_id' => 'string',
            'primary_color' => 'string',
            'secondary_color' => 'string',
            'show_search' => 'boolean',
            'show_delivery_time' => 'boolean',
            'delivery_time_text' => 'string',
            'show_free_shipping_bar' => 'boolean',
            'free_shipping_threshold' => 'float',
            'show_promo_banner' => 'boolean',
            'promo_title' => 'string',
            'promo_subtitle' => 'string',
            'promo_code' => 'string',
            'show_featured_carousel' => 'boolean',
        ];

        $settingsManager = app(CompanySettingsManager::class);
        $settingsManager->setMany($company, $validated, 'catalog', $types);
        PublicCatalogCache::bumpCompany((int) $company->id);

        return response()->json([
            'message' => 'Personalización del catálogo actualizada con éxito.',
            'data' => new CatalogCustomizationResource(array_merge(
                $settingsManager->group($company, 'catalog'),
                ['preview_url' => $company->branches()->withoutTenancy()->first()?->publicCatalogUrl()]
            )),
        ]);
    }

    private function resolveActiveCompany(): Company
    {
        $company = app(TenantContext::class)->company();

        if (! $company) {
            abort(404, 'No se encontró la tienda activa para el usuario.');
        }

        return $company;
    }
}
