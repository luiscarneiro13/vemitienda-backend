<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    private const PLANS = [
        ['name' => 'Starter', 'slug' => 'starter', 'price' => 0, 'interval' => 'monthly', 'features' => ['1 empresa', 'Soporte por email']],
        ['name' => 'Pro', 'slug' => 'pro', 'price' => 29, 'interval' => 'monthly', 'features' => ['Empresas ilimitadas', 'Soporte prioritario', 'API']],
        ['name' => 'Enterprise', 'slug' => 'enterprise', 'price' => 99, 'interval' => 'monthly', 'features' => ['Todo en Pro', 'SLA dedicado', 'Auditoría avanzada']],
    ];

    public function run(): void
    {
        foreach (self::PLANS as $plan) {
            Plan::query()->firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
