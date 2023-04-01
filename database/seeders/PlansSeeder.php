<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = Plan::all();
        if (@count($plan) == 0) {
            $plan = Plan::create(['name' => 'Free', 'quantity' => 0]);
            $plan->save();

            $plan = Plan::create(['name' => 'Catálogo', 'quantity' => 100]);
            $plan->save();

            $plan = Plan::create(['name' => 'Tienda online', 'quantity' => 100]);
            $plan->save();
        }
    }
}
