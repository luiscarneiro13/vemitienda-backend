<?php

namespace App\Domain\Catalog\Models;

use App\Core\Support\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOption extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'product_option_group_id',
        'name',
        'price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (! $model->exists || $model->isDirty('product_option_group_id') || ! $model->company_id) {
                $model->company_id = ($model->relationLoaded('group') && $model->group)
                    ? $model->group->company_id
                    : ProductOptionGroup::withoutTenancy()->whereKey($model->product_option_group_id)->value('company_id');
            }
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductOptionGroup::class, 'product_option_group_id');
    }
}
