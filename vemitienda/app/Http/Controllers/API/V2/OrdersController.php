<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Repositories\OrdersRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            return $this->successResponse(['data' => OrdersRepository::getOrders()]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }


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
