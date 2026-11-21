<?php

declare(strict_types=1);

namespace App\Core\Features\Policies;

use App\Core\Features\Models\CompanyModule;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CompanyModulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CompanyModule');
    }

    public function view(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('View:CompanyModule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CompanyModule');
    }

    public function update(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('Update:CompanyModule');
    }

    public function delete(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('Delete:CompanyModule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CompanyModule');
    }

    public function restore(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('Restore:CompanyModule');
    }

    public function forceDelete(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('ForceDelete:CompanyModule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CompanyModule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CompanyModule');
    }

    public function replicate(AuthUser $authUser, CompanyModule $companyModule): bool
    {
        return $authUser->can('Replicate:CompanyModule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CompanyModule');
    }
}
