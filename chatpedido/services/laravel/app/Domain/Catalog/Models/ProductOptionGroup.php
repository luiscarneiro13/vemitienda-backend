<?php

namespace App\Domain\Catalog\Models;

use App\Core\Support\TenantModel;
use App\Domain\Catalog\Enums\OptionSelectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOptionGroup extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'selection_type',
        'is_required',
        'max_selections',
        'is_removal',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_removal' => 'boolean',
            'max_selections' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (! $model->exists || $model->isDirty('product_id') || ! $model->company_id) {
                $model->company_id = ($model->relationLoaded('product') && $model->product)
                    ? $model->product->company_id
                    : Product::withoutTenancy()->whereKey($model->product_id)->value('company_id');
            }

            if ($model->is_removal) {
                $model->selection_type = OptionSelectionType::Multiple->value;
                $model->is_required = false;
            }

            if ($model->selection_type === OptionSelectionType::Single->value) {
                $model->max_selections = 1;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'product_option_group_id')->orderBy('sort_order');
    }
}
