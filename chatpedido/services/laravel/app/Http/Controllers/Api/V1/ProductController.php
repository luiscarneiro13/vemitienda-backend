<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Listar productos con filtros y paginación.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Product::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $perPage = min(100, max(1, (int) $request->input('per_page', 20)));

        $query = Product::query()
            ->when($company, fn ($q) => $q->where('company_id', $company->id))
            ->when($request->filled('catalog_id'), fn ($q) => $q->where('catalog_id', $request->input('catalog_id')))
            ->when($request->filled('catalog_category_id'), fn ($q) => $q->where('catalog_category_id', $request->input('catalog_category_id')))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->has('is_available'), fn ($q) => $q->where('is_available', $request->boolean('is_available')))
            ->when($request->has('is_featured'), fn ($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.trim((string) $request->input('search')).'%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)->orWhere('description', 'like', $term));
            })
            ->with(['category', 'optionGroups.options'])
            ->withCount('optionGroups')
            ->orderBy('sort_order')
            ->orderBy('id', 'desc');

        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Crear un nuevo producto en el catálogo.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $catalogId = $request->input('catalog_id');

        $validated = $request->validate([
            'catalog_id' => [
                'required',
                'integer',
                $company ? Rule::exists('catalogs', 'id')->where('company_id', $company->id) : 'exists:catalogs,id',
            ],
            'catalog_category_id' => [
                'required',
                'integer',
                Rule::exists('catalog_categories', 'id')->where(function ($q) use ($catalogId, $company) {
                    if ($catalogId) {
                        $q->where('catalog_id', $catalogId);
                    }
                    if ($company) {
                        $q->where('company_id', $company->id);
                    }
                }),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'badge' => ['nullable', 'string', 'max:40'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create($validated);
        $product->load(['category', 'optionGroups.options']);

        return response()->json([
            'message' => 'Producto creado exitosamente.',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Ver un producto específico con sus opciones.
     */
    public function show(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        $product->load(['category', 'optionGroups.options']);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Actualizar los datos de un producto.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $catalogId = $request->input('catalog_id', $product->catalog_id);

        $validated = $request->validate([
            'catalog_id' => [
                'sometimes',
                'required',
                'integer',
                $company ? Rule::exists('catalogs', 'id')->where('company_id', $company->id) : 'exists:catalogs,id',
            ],
            'catalog_category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('catalog_categories', 'id')->where(function ($q) use ($catalogId, $company) {
                    if ($catalogId) {
                        $q->where('catalog_id', $catalogId);
                    }
                    if ($company) {
                        $q->where('company_id', $company->id);
                    }
                }),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'compare_at_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'badge' => ['sometimes', 'nullable', 'string', 'max:40'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product->update($validated);
        $product->load(['category', 'optionGroups.options']);

        return response()->json([
            'message' => 'Producto actualizado exitosamente.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Alternar estado activo del producto.
     */
    public function toggleActive(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $newStatus = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : ! $product->is_active;

        $product->update(['is_active' => $newStatus]);

        return response()->json([
            'message' => 'Estado activo actualizado.',
            'data' => new ProductResource($product->fresh(['category', 'optionGroups.options'])),
        ]);
    }

    /**
     * Alternar disponibilidad (agotado / disponible) del producto.
     */
    public function toggleAvailable(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $newStatus = array_key_exists('is_available', $validated)
            ? (bool) $validated['is_available']
            : ! $product->is_available;

        $product->update(['is_available' => $newStatus]);

        return response()->json([
            'message' => 'Disponibilidad actualizada.',
            'data' => new ProductResource($product->fresh(['category', 'optionGroups.options'])),
        ]);
    }

    /**
     * Subir imagen para el producto.
     */
    public function uploadImage(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $path = $request->file('image')->store('products', 'public');

        $product->update([
            'image_path' => $path,
            'image_url' => null,
        ]);

        return response()->json([
            'message' => 'Imagen del producto subida exitosamente.',
            'data' => new ProductResource($product->fresh(['category', 'optionGroups.options'])),
        ]);
    }

    /**
     * Eliminar la imagen del producto.
     */
    public function deleteImage(Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->update([
            'image_path' => null,
        ]);

        return response()->json([
            'message' => 'Imagen del producto eliminada exitosamente.',
            'data' => new ProductResource($product->fresh(['category', 'optionGroups.options'])),
        ]);
    }

    /**
     * Reordenar productos dentro del catálogo.
     */
    public function reorder(Request $request): JsonResponse
    {
        Gate::authorize('reorder', Product::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => [
                'required',
                'integer',
                $company ? Rule::exists('products', 'id')->where('company_id', $company->id) : 'exists:products,id',
            ],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $item) {
                Product::query()
                    ->where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        if ($company) {
            PublicCatalogCache::bumpCompany((int) $company->id);
        }

        return response()->json([
            'message' => 'Productos reordenados exitosamente.',
        ]);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }
}
