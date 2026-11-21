<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements AuditableContract, FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'current_company_id',
        'current_branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Aísla las consultas de usuarios por la empresa activa: solo son
     * visibles quienes comparten membresía (company_user) con la empresa
     * del TenantContext. Aplica a cualquier operación Eloquent —listados,
     * find, update, delete— no solo a la tabla de Filament.
     * super_admin conserva acceso global, igual que en CompanyScope.
     * Se usa Auth::hasUser() en vez de Auth::check(): si el scope se
     * evalúa durante la propia resolución del usuario autenticado
     * (sesión o token), check() reentraría en el guard y provocaría
     * recursión infinita. Sin usuario resuelto no hay contexto de
     * empresa y la consulta queda sin filtrar, como en CompanyScope.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('companyMembership', function (Builder $builder): void {
            if (! Auth::hasUser() || Auth::user()->hasRole('super_admin')) {
                return;
            }

            // El propio usuario siempre es accesible: un usuario sin
            // contexto de empresa (p. ej. recién registrado) debe poder
            // seguir gestionando su cuenta (perfil, contraseña, baja).
            $authUser = Auth::user();

            $builder->where(
                fn (Builder $query) => $query
                    ->whereKey($authUser->getKey())
                    ->orWhereHas(
                        'companies',
                        fn (Builder $q) => $q->whereKey(app(TenantContext::class)->companyId())
                    )
            );
        });
    }

    /**
     * Empresa principal del usuario (columna company_id).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Todas las empresas a las que pertenece el usuario.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Empresa activa en el contexto actual (usada por el CompanyScope).
     */
    public function currentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    /**
     * Sucursal activa en el contexto actual (usada por TenantContext::branch()).
     */
    public function currentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['super_admin', 'manager', 'store_owner', 'store_admin']),
            default => false,
        };
    }
}
