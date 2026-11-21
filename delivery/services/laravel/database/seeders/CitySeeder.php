<?php

namespace Database\Seeders;

use App\Domain\Locations\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'El Tigre', 'slug' => 'el-tigre', 'state' => 'Anzoátegui'],
            ['name' => 'Puerto La Cruz', 'slug' => 'puerto-la-cruz', 'state' => 'Anzoátegui'],
            ['name' => 'Barcelona', 'slug' => 'barcelona', 'state' => 'Anzoátegui'],
            ['name' => 'Maturín', 'slug' => 'maturin', 'state' => 'Monagas'],
        ];

        foreach ($cities as $city) {
            City::query()->firstOrCreate(['slug' => $city['slug']], [...$city, 'country' => 'VE', 'is_active' => true]);
        }
    }
}
