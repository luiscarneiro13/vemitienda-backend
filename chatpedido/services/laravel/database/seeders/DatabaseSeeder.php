<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Los permisos "Accion:Modelo" de Filament Shield viven en la base de
        // datos, no en una migración: hay que regenerarlos en cada
        // migrate:fresh para que RoleSeeder pueda asignárselos a los roles.
        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--ignore-existing-policies' => true, '--option' => 'permissions', '--no-interaction' => true]);

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CitySeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            PlanSeeder::class,
            ModuleSeeder::class,
            DemoStoreSeeder::class,
        ]);
    }
}
