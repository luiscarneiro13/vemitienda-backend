<?php

namespace App\Domain\Catalog\Models;

use App\Core\Support\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogCategory extends TenantModel
{
    use HasFactory;

    protected $table = 'catalog_categories';

    protected $fillable = [
        'catalog_id',
        'name',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (! $model->exists || $model->isDirty('catalog_id') || ! $model->company_id) {
                $model->company_id = ($model->relationLoaded('catalog') && $model->catalog)
                    ? $model->catalog->company_id
                    : Catalog::withoutTenancy()->whereKey($model->catalog_id)->value('company_id');
            }
        });
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'catalog_category_id')->orderBy('sort_order');
    }
}
