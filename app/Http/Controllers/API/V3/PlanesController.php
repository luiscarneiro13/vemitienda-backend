<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Repositories\PlansRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PlanesController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => PlansRepository::getPlans()]);
    }
}
