<?php

namespace App\Repositories;

use App\Models\Theme;

class ThemesRepository
{
    static function getThemes($limit = 10)
    {
        $filtrar = request()->get('query');

        $datos = Theme::when($filtrar, function ($q) use ($filtrar) {
            $q->where('name', 'like', '%' . $filtrar . '%');
            return $q;
        });

        if ($limit == -1) {
            return $datos->get();
        } else {
            return $datos->paginate($limit);
        }
    }
}
