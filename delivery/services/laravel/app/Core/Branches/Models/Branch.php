<?php

namespace App\Core\Branches\Models;

use App\Core\Companies\Models\Company;
use App\Core\Support\TenantModel;
use App\Core\Tenancy\TenantContext;
use App\Core\Traits\HasCreatedBy;
use App\Core\Traits\HasStatus;
use App\Core\Traits\HasUpdatedBy;
use App\Core\Traits\HasUuid;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Locations\Models\City;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Branch extends TenantModel implements AuditableContract
{
    use Auditable, HasCreatedBy, HasFactory, HasStatus, HasUpdatedBy, HasUuid;

    protected $fillable = [
        'company_id',
        'uuid',
        'name',
        'address',
        'city',
        'city_id',
        'country',
        'timezone',
        'phone',
        'whatsapp_number',
        'email',
        'is_main',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function save(array $options = [])
    {
        if ($this->exists && ! $this->isDirty('company_id')) {
            return parent::save($options);
        }

        $this->company_id ??= app(TenantContext::class)->companyId();

        // El bloqueo de la empresa serializa altas simultáneas sin una restricción
        // única en branches.company_id que impediría la ampliación futura.
        return $this->getConnection()->transaction(function () use ($options) {
            Company::query()->whereKey($this->company_id)->lockForUpdate()->firstOrFail();

            $branches = static::withoutTenancy()->where('company_id', $this->company_id);
            if ($this->exists) {
                $branches->whereKeyNot($this->getKey());
            }

            if ($branches->count() >= config('core.branches_per_company', 1)) {
                throw ValidationException::withMessages([
                    'company_id' => 'Esta empresa ya tiene una sucursal. Por el momento solo se permite una por empresa.',
                ]);
            }

            return parent::save($options);
        });
    }

    public function cityRecord(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    protected function whatsappNumber(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null || trim($value) === '' ? null : preg_replace('/\D+/', '', $value),
        );
    }

    public function publicCatalogUrl(): ?string
    {
        $company = $this->relationLoaded('company') ? $this->company : $this->company()->first();
        if (! $company || ! $this->uuid) {
            return null;
        }

        return route('public.catalog.show', ['companySlug' => $company->slug, 'branchUuid' => $this->uuid]);
    }

    public function isPubliclyAvailable(): bool
    {
        return $this->status === 'active' && filled($this->whatsapp_number) && filled($this->uuid);
    }

    public function catalogEvents(): HasMany
    {
        return $this->hasMany(CatalogEvent::class);
    }
}
