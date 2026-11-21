<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Listar miembros del equipo de la tienda activa.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $users = User::query()
            ->when($company, function ($query) use ($company) {
                $query->whereHas('companies', fn ($q) => $q->where('companies.id', $company->id));
            })
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))
            ->with('roles')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Invitar/crear un nuevo miembro del equipo.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['store_admin', 'user'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $company?->id,
            'current_company_id' => $company?->id,
        ]);

        if ($company) {
            $pivotRole = $validated['role'] === 'store_admin' ? 'admin' : 'member';
            $user->companies()->syncWithoutDetaching([$company->id => ['role' => $pivotRole]]);
        }

        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'Miembro del equipo creado exitosamente.',
            'data' => new UserResource($user->load('roles')),
        ], 201);
    }

    /**
     * Ver un miembro del equipo.
     */
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        if ($company && ! $user->companies()->where('companies.id', $company->id)->exists()) {
            abort(404, 'Usuario no encontrado en este equipo.');
        }

        return response()->json([
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * Actualizar los datos o rol de un miembro del equipo.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        if ($company && ! $user->companies()->where('companies.id', $company->id)->exists()) {
            abort(404, 'Usuario no encontrado en este equipo.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', 'string', Rule::in(['store_admin', 'user'])],
        ]);

        $dataToUpdate = [];
        if (array_key_exists('name', $validated)) {
            $dataToUpdate['name'] = $validated['name'];
        }
        if (array_key_exists('email', $validated)) {
            $dataToUpdate['email'] = $validated['email'];
        }
        if (! empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        if (! empty($dataToUpdate)) {
            $user->update($dataToUpdate);
        }

        // Si se cambia el rol y el usuario objetivo no es store_owner ni super_admin
        if (array_key_exists('role', $validated) && ! $user->hasRole('store_owner') && ! $user->hasRole('super_admin')) {
            $user->syncRoles([$validated['role']]);

            if ($company) {
                $pivotRole = $validated['role'] === 'store_admin' ? 'admin' : 'member';
                $user->companies()->updateExistingPivot($company->id, ['role' => $pivotRole]);
            }
        }

        return response()->json([
            'message' => 'Miembro del equipo actualizado exitosamente.',
            'data' => new UserResource($user->fresh('roles')),
        ]);
    }

    /**
     * Eliminar/desvincular a un miembro del equipo.
     */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        if ($company) {
            $user->companies()->detach($company->id);
        }

        return response()->json([
            'message' => 'Miembro del equipo eliminado exitosamente.',
        ]);
    }
}
