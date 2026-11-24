<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\Catalog;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CatalogController extends Controller
{
    /**
     * Listar catálogos de la tienda activa.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Catalog::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $catalogs = Catalog::query()
            ->when($company, fn ($q) => $q->where('company_id', $company->id))
            ->withCount(['categories', 'products'])
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => CatalogResource::collection($catalogs),
        ]);
    }

    /**
     * Ver detalle de un catálogo específico con sus categorías.
     */
    public function show(Catalog $catalog): JsonResponse
    {
        Gate::authorize('view', $catalog);

        $catalog->load(['categories' => fn ($q) => $q->withCount('products')->orderBy('sort_order')]);
        $catalog->loadCount(['categories', 'products']);

        return response()->json([
            'data' => new CatalogResource($catalog),
        ]);
    }

    /**
     * Actualizar metadatos del catálogo.
     */
    public function update(Request $request, Catalog $catalog): JsonResponse
    {
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
}
