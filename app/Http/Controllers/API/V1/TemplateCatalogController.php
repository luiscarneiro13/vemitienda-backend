<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\TemplateCatalog;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TemplateCatalogController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            return $this->successResponse(['data' => TemplateCatalog::all()]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }
}
