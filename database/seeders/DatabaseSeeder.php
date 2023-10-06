<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // UsersTableSeeder::class,
            // CompanySeeder::class,
            // PlansSeeder::class,
            // PaymentMethodsSeeder::class,
            // ThemesSeeder::class,
            // OrderStatusesSeeder::class,
            // CountriesSeeder::class,

        ]);
        PostCategory::factory(4)->create();
        Tag::factory(8)->create();
        $this->call(PostSeeder::class);
    }
}
