<?php

namespace App\Core\Features\Events;

use App\Core\Companies\Models\Company;
use App\Core\Modules\Models\Module;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeatureDisabled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly Module $module,
    ) {}
}
