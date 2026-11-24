<?php

namespace App\Domain\Catalog\Models;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogEvent extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'company_id',
        'branch_id',
        'catalog_id',
        'event_type',
        'total_cents',
        'items_count',
        'ip_hash',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'total_cents' => 'integer',
            'items_count' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function scopeOrders(Builder $query): Builder
    {
        return $query->where('event_type', 'order_sent');
    }

    public function scopeViews(Builder $query): Builder
    {
        return $query->where('event_type', 'view');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->startOfDay());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }
}
