<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersRepository
{
    static function getOrders()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();

        return Order::with('details.product', 'status')
            ->where('company_id', $company->id)
            ->orderBy('id', 'desc')
            ->get();
    }

    static function updateStatus($order_id, $status_id)
    {
        $order = Order::where('id', $order_id)->first();
        $order->status_id = $status_id;
        $order->save();

        return $order;
    }
}
