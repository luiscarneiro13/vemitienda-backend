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

            $theme = Theme::create(['name' => 'gray-500', 'spanish' => 'gris', 'hexadecimal' => '#6B7280']);
            $theme->save();

            $theme = Theme::create(['name' => 'red-500', 'spanish' => 'rojo', 'hexadecimal' => '#EF4444']);
            $theme->save();

            $theme = Theme::create(['name' => 'yellow-500', 'spanish' => 'amarillo', 'hexadecimal' => '#F59E0B']);
            $theme->save();

            $theme = Theme::create(['name' => 'green-500', 'spanish' => 'verde', 'hexadecimal' => '#10B981']);
            $theme->save();

            $theme = Theme::create(['name' => 'blue-500', 'spanish' => 'azul', 'hexadecimal' => '#3B82F6']);
            $theme->save();

            $theme = Theme::create(['name' => 'indigo-500', 'spanish' => 'azul añil', 'hexadecimal' => '#6366F1']);
            $theme->save();

            $theme = Theme::create(['name' => 'purple-500', 'spanish' => 'púrpura', 'hexadecimal' => '#8B5CF6']);
            $theme->save();

            $theme = Theme::create(['name' => 'black', 'spanish' => 'negro', 'hexadecimal' => '#000000']);
            $theme->save();
        } else {
            // $this->actualizarColor(1, '#6B7280');
            // $this->actualizarColor(2, '#EF4444');
            // $this->actualizarColor(3, '#F59E0B');
            // $this->actualizarColor(4, '#FFFFFF');
            // $this->actualizarColor(5, '#3B82F6');
            // $this->actualizarColor(6, '#6366F1');
            // $this->actualizarColor(7, '#8B5CF6');
            // $this->actualizarColor(8, '#000000');
        }
    }

    function actualizarColor($id, $color)
    {
        $theme = Theme::find($id);
        $theme->hexadecimal = $color;
        $theme->save();
    }
}
