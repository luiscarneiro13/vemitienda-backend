<?php

namespace App\Core\Tenancy;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Tenancy\Resolvers\TenantResolverManager;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Única fuente de verdad sobre "quién es la empresa/sucursal/usuario
 * actuales". Ningún módulo debe consultar Auth::user() directamente para
 * resolver el tenant — siempre a través de este servicio (helper `tenant()`).
 */
class TenantContext
{
    private ?Company $company = null;

    /**
     * ID del usuario para el que se resolvió la empresa cacheada, o
     * `false` si todavía no se ha resuelto nunca. Se compara contra
     * Auth::id() en cada llamada: si el usuario autenticado cambió (login,
     * logout, o el Observer de un modelo resuelve el tenant antes de que
     * el usuario esté autenticado), se re-resuelve en vez de servir un
     * resultado obsoleto.
     */
    private int|false|null $resolvedForUserId = false;

    public function __construct(private readonly TenantResolverManager $resolver) {}

    public function company(): ?Company
    {
        $currentUserId = Auth::id();

        if ($this->resolvedForUserId === false || $this->resolvedForUserId !== $currentUserId) {
            $this->company = $this->resolver->resolve();
            $this->resolvedForUserId = $currentUserId;
        }

        return $this->company;
    }

    public function branch(): ?Branch
    {
        return $this->user()?->currentBranch;
    }

    public function user(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    public function companyId(): ?int
    {
        return $this->company()?->id;
    }

    public function branchId(): ?int
    {
        return $this->branch()?->id;
    }

    public function userId(): ?int
    {
        return $this->user()?->id;
    }

    /**
     * Alias de company() — se mantiene por compatibilidad con el código
     * existente que ya llama a TenantContext::current().
     */
    public function current(): ?Company
    {
        return $this->company();
    }

    public function switchTo(User $user, Company $company): void
    {
        abort_unless(
            $user->companies()->whereKey($company->id)->exists(),
            403,
            'El usuario no pertenece a esta empresa.'
        );

        $attributes = ['current_company_id' => $company->id];

        // La sucursal activa pertenece a la empresa anterior: nunca puede
        // conservarse si no es de la nueva empresa (HasBranch la escribiría
        // en registros nuevos, filtrando datos entre empresas). Se reemplaza
        // por la sucursal principal — misma convención que UserSeeder — o se
        // limpia si la empresa no tiene ninguna. withoutTenancy() es
        // necesario porque CompanyScope aún filtra por la empresa anterior.
        if ($user->currentBranch?->company_id !== $company->id) {
            $attributes['current_branch_id'] = $company->branches()
                ->withoutTenancy()
                ->where('is_main', true)
                ->value('id');
        }

        $user->forceFill($attributes)->save();

        // Las relaciones cacheadas en el modelo apuntarían a la empresa y
        // sucursal anteriores durante el resto del request.
        $user->unsetRelation('currentCompany');
        $user->unsetRelation('currentBranch');

        $this->forgetResolvedCompany();
    }

    public function switchBranchTo(User $user, Branch $branch): void
    {
        abort_unless(
            $branch->company_id === $user->current_company_id,
            403,
            'La sucursal no pertenece a la empresa activa del usuario.'
        );

        $user->forceFill(['current_branch_id' => $branch->id])->save();

        $user->unsetRelation('currentBranch');
    }

    /**
     * Fuerza a re-resolver la empresa activa en la siguiente llamada a
     * company(). Útil tras un switchTo() o en tests.
     */
    public function forgetResolvedCompany(): void
    {
        $this->company = null;
        $this->resolvedForUserId = false;
    }
}
