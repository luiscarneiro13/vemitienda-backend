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

        return Order::with('details', 'status')
            ->where('company_id', $company->id)
            ->get();
    }
}
