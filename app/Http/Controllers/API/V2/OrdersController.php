<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Repositories\OrdersRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use ApiResponser;
    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/orders",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar todas los pedidos del Usuario de la App",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function index()
    {
        try {
            return $this->successResponse(['data' => OrdersRepository::getOrders()]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }
}
