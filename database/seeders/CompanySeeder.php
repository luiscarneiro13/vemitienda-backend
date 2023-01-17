<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\TemplateCatalog;
use App\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'administrador@gmail.com')->first();
        $company = Company::first();

        if (!$company) {

            $templateCatalog1 = TemplateCatalog::create([
                'name' => '1 Columna',
            ]);
            $templateCatalog1->save();

            $templateCatalog2 = TemplateCatalog::create([
                'name' => '2 Columnas',
            ]);
            $templateCatalog2->save();

            $templateCatalog3 = TemplateCatalog::create([
                'name' => '3 Columnas',
            ]);
            $templateCatalog3->save();

            $templateCatalog4 = TemplateCatalog::create([
                'name' => '4 Columnas',
            ]);
            $templateCatalog4->save();

            $comp = Company::create([
                'user_id' => $user->id,
                'name' => 'Sistelconet',
                'slogan' => 'Tu solución en sistemas',
                'email' => 'sistelconet@gmail.com',
                'phone' => '+584248807465',
                'template_catalog_id' => $templateCatalog3->id,
                'background_color_catalog' => '#FFFFFF',
            ]);
            $comp->save();
        }
    }
}
