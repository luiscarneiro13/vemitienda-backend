<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Policies;

use App\Domain\Catalog\Models\Catalog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;

class CatalogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ViewAny:Catalog');
    }

    public function view(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->canAccessRecord($authUser, $catalog, 'View:Catalog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function update(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->canAccessRecord($authUser, $catalog, 'Update:Catalog');
    }

    public function delete(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function restore(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function replicate(AuthUser $authUser, Catalog $catalog): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can($permission);
    }

    private function canAccessRecord(AuthUser $authUser, Model $record, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return true;
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
