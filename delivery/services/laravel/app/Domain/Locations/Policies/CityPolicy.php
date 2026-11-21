<?php

namespace App\Domain\Locations\Policies;

use App\Domain\Locations\Models\City;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function view(AuthUser $authUser, City $city): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function update(AuthUser $authUser, City $city): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function delete(AuthUser $authUser, City $city): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function restore(AuthUser $authUser, City $city): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, City $city): bool
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

    public function replicate(AuthUser $authUser, City $city): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->isSuperAdmin($authUser);
    }

    private function isSuperAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
    }
}
