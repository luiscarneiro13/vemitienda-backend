<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Services\CategoryIconSuggester;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogCategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CatalogCategoryController extends Controller
{
    /**
     * Listar categorías de la tienda activa.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CatalogCategory::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $categories = CatalogCategory::query()
            ->when($company, fn ($q) => $q->where('company_id', $company->id))
            ->when($request->filled('catalog_id'), fn ($q) => $q->where('catalog_id', $request->input('catalog_id')))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->withCount(['products' => fn ($q) => $q->withoutTenancy()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => CatalogCategoryResource::collection($categories),
        ]);
    }

    /**
     * Crear una nueva categoría de catálogo.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', CatalogCategory::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $validated = $request->validate([
            'catalog_id' => [
                'required',
                'integer',
                $company ? Rule::exists('catalogs', 'id')->where('company_id', $company->id) : 'exists:catalogs,id',
            ],
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = CatalogCategory::create($validated);

        return response()->json([
            'message' => 'Categoría creada exitosamente.',
            'data' => new CatalogCategoryResource($category),
        ], 201);
    }

    /**
     * Ver una categoría específica.
     */
    public function show(CatalogCategory $catalogCategory): JsonResponse
    {
        Gate::authorize('view', $catalogCategory);

        $catalogCategory->loadCount(['products' => fn ($q) => $q->withoutTenancy()]);

        return response()->json([
            'data' => new CatalogCategoryResource($catalogCategory),
        ]);
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, CatalogCategory $catalogCategory): JsonResponse
    {
        Gate::authorize('update', $catalogCategory);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $validated = $request->validate([
            'catalog_id' => [
                'sometimes',
                'required',
                'integer',
                $company ? Rule::exists('catalogs', 'id')->where('company_id', $company->id) : 'exists:catalogs,id',
            ],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:60'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $catalogCategory->update($validated);

        return response()->json([
            'message' => 'Categoría actualizada exitosamente.',
            'data' => new CatalogCategoryResource($catalogCategory->fresh()),
        ]);
    }

    /**
     * Activar o desactivar rápidamente una categoría.
     */
    public function toggleActive(Request $request, CatalogCategory $catalogCategory): JsonResponse
    {
        Gate::authorize('update', $catalogCategory);

        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $newStatus = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : ! $catalogCategory->is_active;

        $catalogCategory->update(['is_active' => $newStatus]);

        return response()->json([
            'message' => 'Estado de la categoría actualizado.',
            'data' => new CatalogCategoryResource($catalogCategory->fresh()),
        ]);
    }

    /**
     * Reordenar las categorías del catálogo.
     */
    public function reorder(Request $request): JsonResponse
    {
        Gate::authorize('reorder', CatalogCategory::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => [
                'required',
                'integer',
                $company ? Rule::exists('catalog_categories', 'id')->where('company_id', $company->id) : 'exists:catalog_categories,id',
            ],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $item) {
                CatalogCategory::query()
                    ->where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        if ($company) {
            PublicCatalogCache::bumpCompany((int) $company->id);
        }

        return response()->json([
            'message' => 'Categorías reordenadas exitosamente.',
        ]);
    }

    /**
     * Eliminar una categoría (bloqueada si tiene productos asociados).
     */
    public function destroy(CatalogCategory $catalogCategory): JsonResponse
    {
        Gate::authorize('delete', $catalogCategory);

        if ($catalogCategory->products()->withoutTenancy()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados.',
                'errors' => [
                    'category' => ['No se puede eliminar una categoría con productos. Reasigna o elimina los productos primero.'],
                ],
            ], 422);
        }

        $catalogCategory->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente.',
        ]);
    }

    /**
     * Obtener sugerencias de íconos o emojis clasificados para el selector.
     */
    public function iconSuggestions(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CatalogCategory::class);

        $name = (string) $request->query('name', '');
        $suggested = CategoryIconSuggester::suggest($name);
        $groups = CategoryIconSuggester::groupedIcons();

        return response()->json([
            'suggested' => $suggested,
            'groups' => $groups,
        ]);
    }
}
