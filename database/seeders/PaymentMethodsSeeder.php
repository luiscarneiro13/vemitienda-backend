<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = PaymentMethod::all();
        if (@count($paymentMethods) == 0) {
            $paymentMethods = PaymentMethod::create(['name' => 'Bs Efectivo']);
            $paymentMethods->save();

            $paymentMethods = PaymentMethod::create(['name' => 'Bs Transferencia']);
            $paymentMethods->save();

            $paymentMethods = PaymentMethod::create(['name' => 'Bs Pago Móvil']);
            $paymentMethods->save();

            $paymentMethods = PaymentMethod::create(['name' => '$ Payoneer']);
            $paymentMethods->save();

            $paymentMethods = PaymentMethod::create(['name' => '$ Paypal']);
            $paymentMethods->save();

            $paymentMethods = PaymentMethod::create(['name' => 'USDT Binance']);
            $paymentMethods->save();
        }
    }
}
