<?php

namespace App\Core\Media\Models;

use App\Core\Companies\Models\Company;
use App\Core\Support\CoreModel;
use App\Core\Tenancy\TenantContext;
use App\Core\Traits\HasCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Media extends CoreModel
{
    protected $fillable = [
        'model_type',
        'model_id',
        'collection',
        'disk',
        'path',
        'name',
        'original_name',
        'mime_type',
        'size',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenantMedia', function (Builder $builder): void {
            if (! Auth::hasUser() || Auth::user()->hasRole('super_admin')) {
                return;
            }

            $types = $builder->getModel()
                ->newModelQuery()
                ->distinct()
                ->pluck('model_type')
                ->filter()
                ->all();

            if ($types === []) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $companyId = app(TenantContext::class)->companyId();
            $morphColumn = $builder->qualifyColumn('model_type');
            $idColumn = $builder->qualifyColumn('model_id');

            $builder->where(function (Builder $outer) use ($types, $companyId, $morphColumn, $idColumn): void {
                $outer->whereRaw('1 = 0');

                foreach ($types as $morphType) {
                    $class = Relation::getMorphedModel($morphType) ?? $morphType;

                    if (! is_string($class) || ! class_exists($class) || ! is_subclass_of($class, Model::class)) {
                        continue;
                    }

                    if (is_a($class, Company::class, true)) {
                        if ($companyId !== null) {
                            $outer->orWhere(function (Builder $q) use ($morphColumn, $idColumn, $morphType, $companyId): void {
                                $q->where($morphColumn, $morphType)->where($idColumn, $companyId);
                            });
                        }

                        continue;
                    }

                    if (in_array(HasCompany::class, class_uses_recursive($class), true)) {
                        $instance = new $class;
                        $keyColumn = $instance->qualifyColumn($instance->getKeyName());
                        $companyColumn = $instance->qualifyColumn('company_id');

                        $outer->orWhere(function (Builder $q) use ($morphColumn, $idColumn, $morphType, $class, $keyColumn, $companyColumn, $companyId): void {
                            $q->where($morphColumn, $morphType)
                                ->whereIn(
                                    $idColumn,
                                    $class::withoutTenancy()
                                        ->select($keyColumn)
                                        ->where(function (Builder $w) use ($companyColumn, $companyId): void {
                                            $w->whereNull($companyColumn);
                                            if ($companyId !== null) {
                                                $w->orWhere($companyColumn, $companyId);
                                            }
                                        })
                                );
                        });

                        continue;
                    }

                    if (is_a($class, User::class, true)) {
                        $outer->orWhere(function (Builder $q) use ($morphColumn, $idColumn, $morphType): void {
                            $q->where($morphColumn, $morphType)
                                ->whereIn($idColumn, User::query()->select('users.id'));
                        });

                        continue;
                    }

                    $outer->orWhere($morphColumn, $morphType);
                }
            });
        });

        static::creating(function (Media $media): void {
            if (Auth::hasUser() && ! $media->isOwnedByCurrentTenant()) {
                abort(403, 'No tienes permiso sobre archivos de otra empresa.');
            }
        });

        static::updating(function (Media $media): void {
            if (! Auth::hasUser() || Auth::user()->hasRole('super_admin')) {
                return;
            }

            $original = static::resolveSubjectFor(
                $media->getOriginal('model_type'),
                $media->getOriginal('model_id')
            );

            if ($original === null || ! static::canManageSubject($original) || ! $media->isOwnedByCurrentTenant()) {
                abort(403, 'No tienes permiso sobre archivos de otra empresa.');
            }
        });

        static::deleting(function (Media $media): void {
            if (Auth::hasUser() && ! $media->isOwnedByCurrentTenant()) {
                abort(403, 'No tienes permiso sobre archivos de otra empresa.');
            }
        });
    }

    public function scopeWithoutTenancy(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenantMedia');
    }

    public static function canManageSubject(Model $subject, ?AuthUser $actor = null): bool
    {
        $actor ??= Auth::hasUser() ? Auth::user() : null;

        if ($actor === null || (method_exists($actor, 'hasRole') && $actor->hasRole('super_admin'))) {
            return true;
        }

        $companyId = $actor instanceof User && Auth::hasUser() && (int) Auth::id() === (int) $actor->getKey()
            ? app(TenantContext::class)->companyId()
            : ($actor instanceof User ? $actor->current_company_id : null);

        if ($subject instanceof Company) {
            return $companyId !== null && (int) $subject->getKey() === (int) $companyId;
        }

        if (in_array(HasCompany::class, class_uses_recursive($subject), true)) {
            $subjectCompanyId = $subject->getAttribute('company_id');

            return $companyId !== null
                && $subjectCompanyId !== null
                && (int) $subjectCompanyId === (int) $companyId;
        }

        if ($subject instanceof User) {
            return (int) $subject->getKey() === (int) $actor->getKey()
                || ($companyId !== null && $subject->companies()->whereKey($companyId)->exists());
        }

        return false;
    }

    public static function isGlobalSubject(Model $subject): bool
    {
        if ($subject instanceof Company || $subject instanceof User) {
            return false;
        }

        if (in_array(HasCompany::class, class_uses_recursive($subject), true)) {
            return $subject->getAttribute('company_id') === null;
        }

        return true;
    }

    public function isOwnedByCurrentTenant(?AuthUser $actor = null): bool
    {
        $actor ??= Auth::hasUser() ? Auth::user() : null;

        if ($actor === null || (method_exists($actor, 'hasRole') && $actor->hasRole('super_admin'))) {
            return true;
        }

        $subject = static::resolveSubjectFor($this->model_type, $this->model_id);

        return $subject !== null && static::canManageSubject($subject, $actor);
    }

    public function isAccessibleByCurrentTenant(?AuthUser $actor = null): bool
    {
        $actor ??= Auth::hasUser() ? Auth::user() : null;

        if ($actor === null || (method_exists($actor, 'hasRole') && $actor->hasRole('super_admin'))) {
            return true;
        }

        $subject = static::resolveSubjectFor($this->model_type, $this->model_id);

        return $subject !== null && (static::isGlobalSubject($subject) || static::canManageSubject($subject, $actor));
    }

    protected static function resolveSubjectFor(mixed $morphType, mixed $modelId): ?Model
    {
        if (! $morphType || ! $modelId) {
            return null;
        }

        $class = Relation::getMorphedModel($morphType) ?? $morphType;

        if (! is_string($class) || ! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            return null;
        }

        return $class::withoutGlobalScopes()->find($modelId);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
