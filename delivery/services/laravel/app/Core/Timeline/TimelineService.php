<?php

namespace App\Core\Timeline;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Core\Timeline\Models\TimelineEntry;
use App\Core\Traits\HasCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Registra acciones FUNCIONALES del negocio ("Luis creó un cliente"),
 * legibles para un humano en la UI. No reemplaza a Laravel Auditing
 * (que registra cambios de datos crudos a nivel de modelo) — Timeline
 * cuenta la "historia" de lo que pasó, no el diff de columnas.
 */
class TimelineService
{
    public function __construct(private readonly TenantContext $tenant) {}

    public function log(string $action, string $description, ?Model $subject = null, array $properties = []): TimelineEntry
    {
        $companyId = $this->resolveCompanyId($subject);

        $entry = new TimelineEntry([
            'company_id' => $companyId,
            'user_id' => $this->tenant->userId(),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'created_at' => now(),
        ]);

        $companyId === null ? $entry->saveQuietly() : $entry->save();

        return $entry;
    }

    private function resolveCompanyId(?Model $subject): ?int
    {
        if ($subject instanceof Company) {
            return Company::withTrashed()->whereKey($subject->getKey())->exists()
                ? (int) $subject->getKey()
                : null;
        }

        if ($subject !== null) {
            if (in_array(HasCompany::class, class_uses_recursive($subject), true)) {
                $companyId = $subject->getAttribute('company_id');

                return $companyId !== null ? (int) $companyId : null;
            }

            if ($subject instanceof User) {
                $actor = $this->tenant->user();

                if ($actor && ! $actor->hasRole('super_admin')) {
                    return $this->tenant->companyId();
                }

                return $subject->company_id !== null ? (int) $subject->company_id : null;
            }

            return null;
        }

        if ($this->tenant->user()?->hasRole('super_admin')) {
            return null;
        }

        return $this->tenant->companyId();
    }
}
