<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Visit;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        //Crear la orden
        $order = Order::create([
            "name" => request()->name,
            "email" => request()->email,
            "phone" => request()->phone,
            "total" => request()->total,
            "company_id" => request()->company_id,
            "status_id" => 1
        ]);

        foreach (request()->cart as $value) {
            OrderDetail::create([
                "order_id" => $order->id,
                "product_id" => $value['id'],
                "name" => $value['name'],
                "price" => $value['price'],
                "quantity" => $value['quantity'],
                "image" => $value['attributes']['image']
            ]);

            $product = Product::find($value['id']);
            $newAvailable = $product->available - (int)$value['quantity'];

            if ($newAvailable < 0) {
                $newAvailable = 0;
            }
            $product->available = $newAvailable;
            $product->save();
        }

        $data['order'] = Order::with('details', 'company.logo', 'company.user')->where('id', $order->id)->first();
        $this->emailOrder($data['order'], request()->email);

        return response()->json(["status" => 200]);
    }

    public function prueba()
    {
        // $visits = Visit::all();
        // $i = 1;
        // foreach ($visits as $value) {
        //     $value->id = $i;
        //     $value->save();
        //     $i++;
        // }

        return "HOPLA";
    }

    public function emailOrder($order, $emailComprador)
    {
        $parametros['order'] = $order;
        $parametros['destinatario'] = $order->company->user->email;
        $parametros['type'] = 'OrdenCompra';
        dispatch(new SendEmailJob($parametros));

        $parametros2['order'] = $order;
        $parametros2['destinatario'] = $emailComprador;
        $parametros2['type'] = 'OrdenCompra';

        dispatch(new SendEmailJob($parametros2));
    }
}
