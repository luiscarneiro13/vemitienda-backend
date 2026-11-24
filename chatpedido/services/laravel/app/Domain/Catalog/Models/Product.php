<?php

namespace App\Domain\Catalog\Models;

use App\Core\Support\TenantModel;
use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Product extends TenantModel
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'catalog_id',
        'catalog_category_id',
        'name',
        'description',
        'price',
        'compare_at_price',
        'badge',
        'image_path',
        'image_url',
        'is_active',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
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

            if (! $model->exists || $model->isDirty(['catalog_id', 'catalog_category_id'])) {
                $categoryCatalogId = ($model->relationLoaded('category') && $model->category)
                    ? $model->category->catalog_id
                    : CatalogCategory::withoutTenancy()->whereKey($model->catalog_category_id)->value('catalog_id');

                if ($categoryCatalogId !== (int) $model->catalog_id && $categoryCatalogId !== (string) $model->catalog_id) {
                    throw ValidationException::withMessages([
                        'catalog_category_id' => 'La categoría no pertenece a este catálogo.',
                    ]);
                }
            }
        });
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'catalog_category_id');
    }

    public function optionGroups(): HasMany
    {
        return $this->hasMany(ProductOptionGroup::class)->orderBy('sort_order');
    }

    public function imageUrl(): ?string
    {
        $url = $this->image_url;

        if ($url && filter_var($url, FILTER_VALIDATE_URL) && parse_url($url, PHP_URL_SCHEME) === 'https') {
            return $url;
        }

        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
