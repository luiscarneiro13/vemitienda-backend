<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $themes = Theme::all();
        if (count($themes) == 0) {
            info("LUI");
            $theme = Theme::create(['name' => 'gray-500', 'spanish' => 'gris']);
            $theme->save();

            $theme = Theme::create(['name' => 'red-500', 'spanish' => 'rojo']);
            $theme->save();

            $theme = Theme::create(['name' => 'yellow-500', 'spanish' => 'amarillo']);
            $theme->save();

            $theme = Theme::create(['name' => 'green-500', 'spanish' => 'verde']);
            $theme->save();

            $theme = Theme::create(['name' => 'blue-500', 'spanish' => 'azul']);
            $theme->save();

            $theme = Theme::create(['name' => 'indigo-500', 'spanish' => 'azul añil']);
            $theme->save();

            $theme = Theme::create(['name' => 'purple-500', 'spanish' => 'púrpura']);
            $theme->save();

            $theme = Theme::create(['name' => 'black', 'spanish' => 'negro']);
            $theme->save();
        }
    }
}
