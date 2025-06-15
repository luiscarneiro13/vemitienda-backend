<?php

namespace App\Http\Controllers\WEB\V2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function cartList($slug)
    {
        $data['cartItems'] = Cart::getContent();
        $data['company'] = Company::with('theme')->where('slug', $slug)->first();
        $data['slug'] = $slug;
        return view('V2.cart', $data);
    }


    public function addToCart(Request $request)
    {
        $inserts = [
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'attributes' => array(
                'image' => $request->image,
            )
        ];

        Cart::add($inserts);

        $data = [
            "status" => 200,
            "quantity" => Cart::getTotalQuantity(),
            "message" => 'Producto agregado al carrito',
            "inserts" => $inserts
        ];

        return response()->json($data);
    }

    public function updateCart(Request $request)
    {
        Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
            ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');

        return redirect()->route('cart.list', ['slug' => request()->slug]);
    }

    public function removeCart(Request $request)
    {
        Cart::remove($request->id);
        session()->flash('success', 'Item Cart Remove Successfully !');
        return response()->json(["status" => 'ok']);
        // return redirect()->route('cart.list', ['slug' => request()->slug]);
    }

    public function clearAllCart()
    {
        Cart::clear();

        session()->flash('success', 'All Item Cart Clear Successfully !');

        return redirect()->route('cart.list', ['slug' => request()->slug]);
    }

}
