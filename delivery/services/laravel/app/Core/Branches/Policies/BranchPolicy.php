<?php

declare(strict_types=1);

namespace App\Core\Branches\Policies;

use App\Core\Branches\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ViewAny:Branch');
    }

    public function view(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'View:Branch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Create:Branch');
    }

    public function update(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'Update:Branch');
    }

    public function delete(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'Delete:Branch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'DeleteAny:Branch');
    }

    public function restore(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'Restore:Branch');
    }

    public function forceDelete(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'ForceDelete:Branch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ForceDeleteAny:Branch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'RestoreAny:Branch');
    }

    public function replicate(AuthUser $authUser, Branch $branch): bool
    {
        return $this->canAccessBranch($authUser, $branch, 'Replicate:Branch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Reorder:Branch');
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can($permission);
    }

    private function canAccessBranch(AuthUser $authUser, Branch $branch, string $permission): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        return $authUser->can($permission)
            && $authUser instanceof User
            && $authUser->companies()->whereKey($branch->company_id)->exists();
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
