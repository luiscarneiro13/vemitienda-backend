<?php

namespace App\Core\Branches\Observers;

use App\Core\Branches\Events\BranchCreated;
use App\Core\Branches\Events\BranchDeleted;
use App\Core\Branches\Events\BranchUpdated;
use App\Core\Branches\Models\Branch;
use App\Core\Timeline\TimelineService;

class BranchObserver
{
    public function __construct(private readonly TimelineService $timeline) {}

    public function created(Branch $branch): void
    {
        BranchCreated::dispatch($branch);

        $this->timeline->log('branch.created', "Se creó la sucursal \"{$branch->name}\"", $branch);
    }

    public function updated(Branch $branch): void
    {
        BranchUpdated::dispatch($branch);

        $this->timeline->log('branch.updated', "Se actualizó la sucursal \"{$branch->name}\"", $branch);
    }

    public function deleted(Branch $branch): void
    {
        BranchDeleted::dispatch($branch);

        $this->timeline->log('branch.deleted', "Se eliminó la sucursal \"{$branch->name}\"", $branch);
    }
}
