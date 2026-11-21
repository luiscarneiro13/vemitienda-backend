<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductOptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductOptionController extends Controller
{
    /**
     * Crear una opción dentro de un grupo de opciones.
     */
    public function store(Request $request, ProductOptionGroup $optionGroup): JsonResponse
    {
        $optionGroup->loadMissing('product');
        Gate::authorize('update', $optionGroup->product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $option = $optionGroup->options()->create($validated);

        return response()->json([
            'message' => 'Opción creada exitosamente.',
            'data' => new ProductOptionResource($option),
        ], 201);
    }

    /**
     * Actualizar una opción de producto.
     */
    public function update(Request $request, ProductOption $option): JsonResponse
    {
        $option->loadMissing('group.product');
        Gate::authorize('update', $option->group->product);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $option->update($validated);

        return response()->json([
            'message' => 'Opción actualizada exitosamente.',
            'data' => new ProductOptionResource($option->fresh()),
        ]);
    }

    /**
     * Alternar estado activo de una opción de producto.
     */
    public function toggleActive(Request $request, ProductOption $option): JsonResponse
    {
        $option->loadMissing('group.product');
        Gate::authorize('update', $option->group->product);

        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $newStatus = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : ! $option->is_active;

        $option->update(['is_active' => $newStatus]);

        return response()->json([
            'message' => 'Estado de la opción actualizado.',
            'data' => new ProductOptionResource($option->fresh()),
        ]);
    }

    /**
     * Reordenar opciones dentro de un grupo de opciones.
     */
    public function reorder(Request $request, ProductOptionGroup $optionGroup): JsonResponse
    {
        $optionGroup->loadMissing('product');
        Gate::authorize('update', $optionGroup->product);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => [
                'required',
                'integer',
                Rule::exists('product_options', 'id')->where('product_option_group_id', $optionGroup->id),
            ],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $item) {
                ProductOption::query()
                    ->where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        if ($optionGroup->company_id) {
            PublicCatalogCache::bumpCompany((int) $optionGroup->company_id);
        }

        return response()->json([
            'message' => 'Opciones reordenadas exitosamente.',
        ]);
    }

    /**
     * Eliminar una opción de producto.
     */
    public function destroy(ProductOption $option): JsonResponse
    {
        $option->loadMissing('group.product');
        Gate::authorize('update', $option->group->product);

        $option->delete();

        return response()->json([
            'message' => 'Opción eliminada exitosamente.',
        ]);
    }
}
