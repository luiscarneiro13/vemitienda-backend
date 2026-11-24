<?php

declare(strict_types=1);

namespace App\Core\Timeline\Policies;

use App\Core\Timeline\Models\TimelineEntry;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TimelineEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TimelineEntry');
    }

    public function view(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('View:TimelineEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TimelineEntry');
    }

    public function update(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('Update:TimelineEntry');
    }

    public function delete(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('Delete:TimelineEntry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TimelineEntry');
    }

    public function restore(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('Restore:TimelineEntry');
    }

    public function forceDelete(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('ForceDelete:TimelineEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TimelineEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TimelineEntry');
    }

    public function replicate(AuthUser $authUser, TimelineEntry $timelineEntry): bool
    {
        return $authUser->can('Replicate:TimelineEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TimelineEntry');
    }
}
