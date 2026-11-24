<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Policies;

use App\Domain\Catalog\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ViewAny:Product');
    }

    public function view(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'View:Product');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Create:Product');
    }

    public function update(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'Update:Product');
    }

    public function delete(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'Delete:Product');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'DeleteAny:Product');
    }

    public function restore(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'Restore:Product');
    }

    public function forceDelete(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'ForceDelete:Product');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ForceDeleteAny:Product');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'RestoreAny:Product');
    }

    public function replicate(AuthUser $authUser, Product $product): bool
    {
        return $this->canAccessRecord($authUser, $product, 'Replicate:Product');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Reorder:Product');
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
