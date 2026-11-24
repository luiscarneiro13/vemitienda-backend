<?php

namespace App\Core\Modules\Events;

use App\Core\Modules\Models\Module;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleInstalled
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Module $module) {}
}
