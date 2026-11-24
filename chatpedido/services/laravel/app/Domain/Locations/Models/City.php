<?php

namespace App\Domain\Locations\Models;

use App\Core\Branches\Models\Branch;
use App\Core\Support\CoreModel;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends CoreModel
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'state',
        'country',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'city_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
