<?php

namespace App\Core\Branches\Events;

use App\Core\Branches\Models\Branch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BranchUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Branch $branch) {}
}
