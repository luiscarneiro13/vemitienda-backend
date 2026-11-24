<?php

namespace App\Core\Modules\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ModuleRemoved
{
    use Dispatchable;

    public function __construct(public readonly string $slug) {}
}
