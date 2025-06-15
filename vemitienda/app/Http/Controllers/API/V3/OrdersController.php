<?php

namespace App\Http\Controllers\API\V3;

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
     *     path="/v3/orders",
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

    /**
     * @OA\Post(
     *     tags={"Orders"},
     *     path="/v3/updateStatus",
     *     security={{"bearer_token":{}}},
     *     summary="Se actualiza el status del pedido",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Status del pedido",
     *        @OA\JsonContent(
     *           required={"order_id","status_id"},
     *           @OA\Property(property="order_id", type="integer", format="order_id", example="1"),
     *           @OA\Property(property="status_id", type="integer", format="status_id", example="2"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */

    public function updateStatus()
    {
        $order_id = request()->order_id;
        $status_id = request()->status_id;
        try {
            return $this->successResponse(['data' => OrdersRepository::updateStatus($order_id, $status_id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }
}
