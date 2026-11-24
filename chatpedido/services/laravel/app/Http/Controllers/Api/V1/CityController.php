<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Locations\Models\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Listar ciudades activas en el sistema.
     */
    public function index(Request $request): JsonResponse
    {
        $cities = City::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => CityResource::collection($cities),
        ]);
    }
}
