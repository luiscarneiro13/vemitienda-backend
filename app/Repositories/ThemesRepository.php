<?php

namespace App\Repositories;

use App\Models\Theme;

class ThemesRepository
{
    static function getThemes($limit = 10)
    {
        $filtrar = request()->get('query');

        return Theme::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }
}
