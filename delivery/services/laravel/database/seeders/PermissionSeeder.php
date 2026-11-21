<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Permisos de negocio (formato "recurso.accion"), independientes de los
     * permisos "Accion:Modelo" que genera Filament Shield por cada Resource.
     */
    private const PERMISSIONS = [
        'users.view', 'users.create', 'users.update', 'users.delete',
        'roles.view', 'roles.create', 'roles.update', 'roles.delete',
        'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
        'companies.view', 'companies.create', 'companies.update', 'companies.delete',
        'settings.view', 'settings.update',
    ];

    public function run(): void
    {
        $now = now();
        $rows = array_map(fn (string $permission): array => [
            'name' => $permission,
            'guard_name' => 'web',
            'created_at' => $now,
            'updated_at' => $now,
        ], self::PERMISSIONS);

        Permission::query()->insertOrIgnore($rows);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
