<?php

namespace App\Core\Timeline\Models;

use App\Core\Support\TenantModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TimelineEntry extends TenantModel
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'properties',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
