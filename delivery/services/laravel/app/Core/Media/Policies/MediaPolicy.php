<?php

declare(strict_types=1);

namespace App\Core\Media\Policies;

use App\Core\Media\Models\Media;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('ViewAny:Media');
    }

    public function view(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('View:Media'))
            && $media->isAccessibleByCurrentTenant($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('Create:Media');
    }

    public function update(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('Update:Media'))
            && $media->isOwnedByCurrentTenant($authUser);
    }

    public function delete(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('Delete:Media'))
            && $media->isOwnedByCurrentTenant($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('DeleteAny:Media');
    }

    public function restore(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('Restore:Media'))
            && $media->isOwnedByCurrentTenant($authUser);
    }

    public function forceDelete(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('ForceDelete:Media'))
            && $media->isOwnedByCurrentTenant($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('ForceDeleteAny:Media');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('RestoreAny:Media');
    }

    public function replicate(AuthUser $authUser, Media $media): bool
    {
        return ($this->isSuperAdmin($authUser) || $authUser->can('Replicate:Media'))
            && $media->isOwnedByCurrentTenant($authUser);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser) || $authUser->can('Reorder:Media');
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
