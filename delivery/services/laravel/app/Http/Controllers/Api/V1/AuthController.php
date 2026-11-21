<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Resources\V1\StoreResource;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Iniciar sesión en la API y obtener Bearer token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $throttleKey = Str::lower($validated['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ["Demasiados intentos de acceso. Por favor, intenta de nuevo en {$seconds} segundos."],
            ]);
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        $allowedRoles = ['super_admin', 'store_owner', 'store_admin'];
        if (! $user->hasAnyRole($allowedRoles)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a la aplicación móvil de gestión.',
            ], 403);
        }

        // Asegurar que tenga una empresa activa
        if (! $user->hasRole('super_admin')) {
            $userCompanies = $user->companies()->where('companies.is_active', true)->get();

            if ($userCompanies->isEmpty()) {
                return response()->json([
                    'message' => 'Tu usuario no tiene ninguna tienda activa asignada.',
                ], 403);
            }

            if (! $user->current_company_id || ! $userCompanies->contains('id', $user->current_company_id)) {
                $defaultCompany = $userCompanies->first();
                $user->current_company_id = $defaultCompany->id;
                $user->current_branch_id = $defaultCompany->branches()->value('id');
                $user->save();
            }
        }

        $deviceName = $validated['device_name'] ?? 'Mobile App';
        $token = $user->createToken($deviceName);

        $activeCompany = $user->currentCompany
            ?? $user->companies()->where('companies.is_active', true)->first();

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'user' => new UserResource($user),
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'active_store' => $activeCompany ? new StoreResource($activeCompany) : null,
            'stores' => CompanyResource::collection($user->companies),
        ]);
    }

    /**
     * Obtener el perfil del usuario autenticado y su tienda activa.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var TenantContext $tenant */
        $tenant = app(TenantContext::class);
        $activeCompany = $tenant->company();

        return response()->json([
            'user' => new UserResource($user),
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'active_store' => $activeCompany ? new StoreResource($activeCompany) : null,
            'stores' => CompanyResource::collection($user->companies),
        ]);
    }

    /**
     * Cambiar de tienda activa (para usuarios multi-tienda).
     */
    public function switchCompany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer'],
        ]);

        /** @var User $user */
        $user = $request->user();

        /** @var Company $company */
        $company = Company::query()->findOrFail($validated['company_id']);

        if (! $user->hasRole('super_admin') && ! $user->companies()->whereKey($company->id)->exists()) {
            return response()->json([
                'message' => 'No tienes acceso a esta tienda.',
            ], 403);
        }

        app(TenantContext::class)->switchTo($user, $company);

        return response()->json([
            'message' => 'Tienda activa actualizada.',
            'active_store' => new StoreResource($company),
        ]);
    }

    /**
     * Cerrar sesión y revocar el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}
