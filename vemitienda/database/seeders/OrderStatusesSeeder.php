<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oe = OrderStatus::all();
        if (count($oe) == 0) {

            $oe = DB::table('order_statuses')->insert(['name' => 'Creada']);

            $oe = DB::table('order_statuses')->insert(['name' => 'En Proceso']);

            $theme = DB::table('order_statuses')->insert(['name' => 'Finalizada']);
        }
    }
}
