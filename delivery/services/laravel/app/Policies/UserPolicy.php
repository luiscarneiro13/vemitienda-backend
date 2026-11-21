<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser, User $user): bool
    {
        return $this->canAccessUser($authUser, $user, 'View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, User $user): bool
    {
        // El usuario dueño de la tienda no lo puede editar el mismo dueño de la tienda
        // (ni otro dueño o admin de la tienda; solo super_admin).
        if ($user->hasRole('store_owner') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        // Si el usuario objetivo es super_admin, solo otro super_admin puede editarlo
        if ($user->hasRole('super_admin') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->canAccessUser($authUser, $user, 'Update:User');
    }

    public function delete(AuthUser $authUser, User $user): bool
    {
        // Ningún usuario puede eliminarse a sí mismo
        if ($user->is($authUser) || $user->id === $authUser->id) {
            return false;
        }

        // Para borrar al dueño de la tienda solo lo puede hacer el admin (super_admin)
        if ($user->hasRole('store_owner') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        // Para borrar a un super_admin solo lo puede hacer otro super_admin
        if ($user->hasRole('super_admin') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->canAccessUser($authUser, $user, 'Delete:User');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:User');
    }

    public function restore(AuthUser $authUser, User $user): bool
    {
        if ($user->is($authUser) || $user->id === $authUser->id) {
            return false;
        }

        if ($user->hasRole('store_owner') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->canAccessUser($authUser, $user, 'Restore:User');
    }

    public function forceDelete(AuthUser $authUser, User $user): bool
    {
        if ($user->is($authUser) || $user->id === $authUser->id) {
            return false;
        }

        if ($user->hasRole('store_owner') && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->canAccessUser($authUser, $user, 'ForceDelete:User');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(AuthUser $authUser, User $user): bool
    {
        return $this->canAccessUser($authUser, $user, 'Replicate:User');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }

    private function canAccessUser(AuthUser $authUser, User $user, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        if (! $authUser->can($permission) || ! $authUser instanceof User) {
            return false;
        }

        if ($user->is($authUser)) {
            return true;
        }

        $companyIds = $user->companies()->pluck('companies.id');
        if ($user->company_id) {
            $companyIds->push($user->company_id);
        }

        return $authUser->companies()
            ->whereIn('companies.id', $companyIds->unique())
            ->exists();
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
