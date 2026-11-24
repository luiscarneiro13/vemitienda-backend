<?php

namespace App\Core\Companies\Models;

use App\Core\Branches\Models\Branch;
use App\Core\Support\CompanyModel;
use App\Core\Traits\HasCreatedBy;
use App\Core\Traits\HasDeletedBy;
use App\Core\Traits\HasSlug;
use App\Core\Traits\HasStatus;
use App\Core\Traits\HasUpdatedBy;
use App\Core\Traits\HasUuid;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Company extends CompanyModel implements AuditableContract
{
    use Auditable, HasCreatedBy, HasDeletedBy, HasFactory, HasSlug, HasStatus, HasUpdatedBy, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'legal_name',
        'tax_id',
        'address',
        'email',
        'phone',
        'country',
        'timezone',
        'language',
        'currency',
        'logo',
        'store_category',
        'store_description',
        'cover_image_url',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function coverImageUrl(): ?string
    {
        $url = $this->cover_image_url;

        return $url && filter_var($url, FILTER_VALIDATE_URL) && parse_url($url, PHP_URL_SCHEME) === 'https'
            ? $url
            : null;
    }

    public function logoUrl(): ?string
    {
        $logo = $this->logo;

        if (! $logo) {
            return null;
        }

        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            return parse_url($logo, PHP_URL_SCHEME) === 'https' ? $logo : null;
        }

        return Storage::disk('public')->url($logo);
    }

    public function initials(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(Catalog::class);
    }

    public function defaultCatalog(): HasOne
    {
        return $this->hasOne(Catalog::class)->where('is_default', true);
    }

    public function catalogEvents(): HasMany
    {
        return $this->hasMany(CatalogEvent::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }
}
