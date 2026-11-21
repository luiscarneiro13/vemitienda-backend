<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Policies;

use App\Domain\Catalog\Models\CatalogCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;

class CatalogCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ViewAny:CatalogCategory');
    }

    public function view(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'View:CatalogCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Create:CatalogCategory');
    }

    public function update(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'Update:CatalogCategory');
    }

    public function delete(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'Delete:CatalogCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'DeleteAny:CatalogCategory');
    }

    public function restore(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'Restore:CatalogCategory');
    }

    public function forceDelete(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'ForceDelete:CatalogCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ForceDeleteAny:CatalogCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'RestoreAny:CatalogCategory');
    }

    public function replicate(AuthUser $authUser, CatalogCategory $catalogCategory): bool
    {
        return $this->canAccessRecord($authUser, $catalogCategory, 'Replicate:CatalogCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Reorder:CatalogCategory');
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return false;
        }

        return $authUser->can($permission);
    }

    private function canAccessRecord(AuthUser $authUser, Model $record, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return false;
        }

        return $authUser->can($permission)
            && $authUser instanceof User
            && $authUser->companies()->whereKey($record->company_id)->exists();
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
