<?php

declare(strict_types=1);

namespace App\Policies;

use App\Core\Companies\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ViewAny:Company');
    }

    public function view(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'View:Company');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Create:Company');
    }

    public function update(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'Update:Company');
    }

    public function delete(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'Delete:Company');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'DeleteAny:Company');
    }

    public function restore(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'Restore:Company');
    }

    public function forceDelete(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'ForceDelete:Company');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ForceDeleteAny:Company');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'RestoreAny:Company');
    }

    public function replicate(AuthUser $authUser, Company $company): bool
    {
        return $this->canAccessCompany($authUser, $company, 'Replicate:Company');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Reorder:Company');
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can($permission);
    }

    private function canAccessCompany(AuthUser $authUser, Company $company, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        return $authUser->can($permission)
            && $authUser instanceof User
            && $authUser->companies()->whereKey($company->getKey())->exists();
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
