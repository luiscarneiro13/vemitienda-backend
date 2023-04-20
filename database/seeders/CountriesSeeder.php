<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Country = Country::first();

        if (!$Country) {
            $comp = Country::create(['name' => 'Venezuela']);
            $comp->save();

            $comp = Country::create(['name' => 'Colombia']);
            $comp->save();
        }
    }
}
