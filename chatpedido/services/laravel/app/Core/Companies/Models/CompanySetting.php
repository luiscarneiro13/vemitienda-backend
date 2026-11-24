<?php

namespace App\Core\Companies\Models;

use App\Core\Support\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends TenantModel
{
    protected $table = 'company_settings';

    protected $fillable = [
        'company_id',
        'group',
        'key',
        'value',
        'type',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
