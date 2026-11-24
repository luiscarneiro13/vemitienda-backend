<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Concerns;

use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

trait SeedsApiPermissions
{
    protected function seedPermissionsAndRoles(): void
    {
        $permissions = [
            'ViewAny:Company', 'View:Company', 'Update:Company',
            'ViewAny:Branch', 'View:Branch', 'Create:Branch', 'Update:Branch', 'Delete:Branch', 'DeleteAny:Branch',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User',
            'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
            'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
            'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $this->seed(RoleSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
