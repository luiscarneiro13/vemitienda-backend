<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Enums\OptionSelectionType;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductOptionGroupResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductOptionGroupController extends Controller
{
    /**
     * Listar grupos de opciones de un producto.
     */
    public function index(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        $groups = $product->optionGroups()->with('options')->get();

        return response()->json([
            'data' => ProductOptionGroupResource::collection($groups),
        ]);
    }

    /**
     * Crear un grupo de opciones para un producto.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'selection_type' => ['required', Rule::enum(OptionSelectionType::class)],
            'is_required' => ['nullable', 'boolean'],
            'max_selections' => ['nullable', 'integer', 'min:1'],
            'is_removal' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $group = $product->optionGroups()->create($validated);
        $group->load('options');

        return response()->json([
            'message' => 'Grupo de opciones creado exitosamente.',
            'data' => new ProductOptionGroupResource($group),
        ], 201);
    }

    /**
     * Ver un grupo de opciones específico con sus opciones.
     */
    public function show(ProductOptionGroup $optionGroup): JsonResponse
    {
        $optionGroup->loadMissing('product');
        Gate::authorize('view', $optionGroup->product);

        $optionGroup->load('options');

        return response()->json([
            'data' => new ProductOptionGroupResource($optionGroup),
        ]);
    }

    /**
     * Actualizar un grupo de opciones.
     */
    public function update(Request $request, ProductOptionGroup $optionGroup): JsonResponse
    {
        $optionGroup->loadMissing('product');
        Gate::authorize('update', $optionGroup->product);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'selection_type' => ['sometimes', 'required', Rule::enum(OptionSelectionType::class)],
            'is_required' => ['sometimes', 'boolean'],
            'max_selections' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'is_removal' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $optionGroup->update($validated);
        $optionGroup->load('options');

        return response()->json([
            'message' => 'Grupo de opciones actualizado exitosamente.',
            'data' => new ProductOptionGroupResource($optionGroup),
        ]);
    }

    /**
     * Reordenar grupos de opciones de un producto.
     */
    public function reorder(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => [
                'required',
                'integer',
                Rule::exists('product_option_groups', 'id')->where('product_id', $product->id),
            ],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $item) {
                ProductOptionGroup::query()
                    ->where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        if ($product->company_id) {
            PublicCatalogCache::bumpCompany((int) $product->company_id);
        }

        return response()->json([
            'message' => 'Grupos de opciones reordenados exitosamente.',
        ]);
    }

    /**
     * Eliminar un grupo de opciones y sus opciones asociadas.
     */
    public function destroy(ProductOptionGroup $optionGroup): JsonResponse
    {
        $optionGroup->loadMissing('product');
        Gate::authorize('update', $optionGroup->product);

        $optionGroup->delete();

        return response()->json([
            'message' => 'Grupo de opciones eliminado exitosamente.',
        ]);
    }
}
