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
        info($company);
        if (!$company) {

            $templateCatalog = TemplateCatalog::create([
                'name' => '2 Columnas',
            ]);
            $templateCatalog->save();

            $templateCatalog2 = TemplateCatalog::create([
                'name' => '3 Columnas',
            ]);
            $templateCatalog2->save();

            $comp = Company::create([
                'user_id' => $user->id,
                'name' => 'Sistelconet',
                'slogan' => 'Tu solución en sistemas',
                'email' => 'sistelconet@gmail.com',
                'phone' => '+584248807465',
                'template_catalog_id' => $templateCatalog->id,
                'background_color_catalog' => '#FFFFFF',
            ]);
            $comp->save();
        }
    }
}
