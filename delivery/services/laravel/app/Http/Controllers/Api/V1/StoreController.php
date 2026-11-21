<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BranchResource;
use App\Http\Resources\V1\StoreResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    /**
     * Listar las tiendas a las que tiene acceso el usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        Gate::authorize('viewAny', Company::class);

        $companies = $user->hasRole('super_admin')
            ? Company::query()->with('branches.cityRecord')->orderBy('name')->paginate(20)
            : $user->companies()->with('branches.cityRecord')->orderBy('name')->paginate(20);

        return response()->json([
            'data' => StoreResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'total' => $companies->total(),
            ],
        ]);
    }

    /**
     * Obtener el perfil completo de la tienda activa y su sucursal única.
     */
    public function show(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('view', $company);

        return response()->json([
            'data' => new StoreResource($company),
        ]);
    }

    /**
     * Actualizar los datos de la tienda activa y sincronizar con su sucursal única.
     */
    public function update(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        /** @var User $user */
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'tax_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'store_category' => ['sometimes', 'nullable', 'string', 'max:100'],
            'store_description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'cover_image_url' => ['sometimes', 'nullable', 'url:https', 'max:2048'],
            'whatsapp_number' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^\+?[\d\s\-()]+$/',
                function (string $attribute, $value, \Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 10 || strlen($digits) > 15) {
                        $fail('El WhatsApp debe tener entre 10 y 15 dígitos, incluyendo el código de país.');
                    }
                },
            ],
            'city_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],
            // Restringidos a super_admin
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('companies', 'slug')->ignore($company->id),
                Rule::prohibitedIf(! $isSuperAdmin),
            ],
            'is_active' => [
                'sometimes',
                'boolean',
                Rule::prohibitedIf(! $isSuperAdmin),
            ],
        ]);

        $companyData = collect($validated)->except(['whatsapp_number', 'city_id'])->all();
        if (! empty($companyData)) {
            $company->update($companyData);
        }

        // Sincronizar con la sucursal única
        $branch = $company->branches()->withoutTenancy()->first();
        $branchUpdates = [];

        if (array_key_exists('whatsapp_number', $validated)) {
            $branchUpdates['whatsapp_number'] = $validated['whatsapp_number'];
        }

        if (array_key_exists('city_id', $validated) && $isSuperAdmin) {
            $branchUpdates['city_id'] = $validated['city_id'];
        }

        if (! empty($validated['address'])) {
            $branchUpdates['address'] = $validated['address'];
        }

        if ($branch && ! empty($branchUpdates)) {
            $branch->update($branchUpdates);
        } elseif (! $branch && (! empty($validated['whatsapp_number']) || ! empty($validated['city_id']))) {
            $company->branches()->create([
                'name' => 'Sede Principal',
                'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'address' => $company->address,
                'status' => 'active',
                'is_main' => true,
            ]);
        }

        return response()->json([
            'message' => 'Tienda actualizada exitosamente.',
            'data' => new StoreResource($company->fresh()),
        ]);
    }

    /**
     * Subir o actualizar el logo de la tienda.
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        $request->validate([
            'logo' => ['required', 'file', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        // Borrar logo anterior si era un archivo local
        if ($company->logo && ! filter_var($company->logo, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($company->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $company->update(['logo' => $path]);

        return response()->json([
            'message' => 'Logo actualizado con éxito.',
            'data' => [
                'logo' => $company->logo,
                'logo_url' => $company->logoUrl(),
            ],
        ]);
    }

    /**
     * Eliminar el logo actual de la tienda.
     */
    public function deleteLogo(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        if ($company->logo && ! filter_var($company->logo, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->update(['logo' => null]);

        return response()->json([
            'message' => 'Logo eliminado con éxito.',
            'data' => [
                'logo' => null,
                'logo_url' => null,
            ],
        ]);
    }

    /**
     * Obtener los datos de la sucursal única de la tienda activa.
     */
    public function branch(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('view', $company);

        $branch = $company->branches()->withoutTenancy()->with('cityRecord')->firstOrFail();
        Gate::authorize('view', $branch);

        return response()->json([
            'data' => new BranchResource($branch),
        ]);
    }

    /**
     * Actualizar los datos de la sucursal única de la tienda activa.
     */
    public function updateBranch(Request $request): JsonResponse
    {
        $company = $this->resolveActiveCompany();
        Gate::authorize('update', $company);

        $branch = $company->branches()->withoutTenancy()->firstOrFail();
        Gate::authorize('update', $branch);

        /** @var User $user */
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'whatsapp_number' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^\+?[\d\s\-()]+$/',
                function (string $attribute, $value, \Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 10 || strlen($digits) > 15) {
                        $fail('El WhatsApp debe tener entre 10 y 15 dígitos, incluyendo el código de país.');
                    }
                },
            ],
            // Campos restringidos a super_admin
            'city_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('is_active', true),
                Rule::prohibitedIf(! $isSuperAdmin),
            ],
            'status' => [
                'sometimes',
                'string',
                'in:active,inactive',
                Rule::prohibitedIf(! $isSuperAdmin),
            ],
        ]);

        $branch->update($validated);

        if (! empty($validated['address']) && blank($company->address)) {
            $company->update(['address' => $validated['address']]);
        }

        return response()->json([
            'message' => 'Sucursal actualizada exitosamente.',
            'data' => new BranchResource($branch->fresh('cityRecord')),
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
