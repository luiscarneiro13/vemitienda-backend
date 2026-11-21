<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    protected $table = 'company_user';

    protected $fillable = [
        'company_id',
        'user_id',
        'role',
    ];
}
