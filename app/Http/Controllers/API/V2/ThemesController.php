<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ThemesController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => ThemesRepository::getThemes(-1)]);
    }
}
