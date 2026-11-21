<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Permisos de negocio ("recurso.accion") por rol. super_admin recibe
     * todos los permisos existentes (además del bypass de Shield vía Gate::before).
     */
    private const BUSINESS_PERMISSIONS = [
        'store_owner' => [],
        'store_admin' => [],
        'user' => [],
    ];

    /**
     * Permisos generados por Filament Shield (formato "Accion:Modelo") por rol.
     */
    private const SHIELD_PERMISSION_SUFFIXES = [
        'store_owner' => [
            'ViewAny:Company', 'View:Company', 'Update:Company',
            'ViewAny:Branch', 'View:Branch', 'Create:Branch', 'Update:Branch', 'Delete:Branch', 'DeleteAny:Branch',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User',
            'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
            'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
            'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
        ],
        'store_admin' => [
            'ViewAny:Company', 'View:Company', 'Update:Company',
            'ViewAny:Branch', 'View:Branch', 'Create:Branch', 'Update:Branch', 'Delete:Branch', 'DeleteAny:Branch',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User',
            'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
            'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
            'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
        ],
        'user' => [],
    ];

    public function run(): void
    {
        $allPermissions = Permission::all();
        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $superAdmin->syncPermissions($allPermissions);

        $availableNames = $allPermissions->pluck('name')->flip();

        foreach (self::BUSINESS_PERMISSIONS as $roleName => $businessPermissions) {
            $role = Role::findOrCreate($roleName, 'web');

            $shieldPermissions = array_filter(
                self::SHIELD_PERMISSION_SUFFIXES[$roleName],
                fn (string $permission): bool => isset($availableNames[$permission])
            );

            $role->syncPermissions(array_merge($businessPermissions, $shieldPermissions));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
