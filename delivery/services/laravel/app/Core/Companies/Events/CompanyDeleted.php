<?php

namespace App\Core\Companies\Events;

use App\Core\Companies\Models\Company;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Company $company) {}
}
