<?php

namespace App\Services;

use App\Core\Companies\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;

class SubscriptionService
{
    public function subscribe(Company $company, Plan $plan): Subscription
    {
        return Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);
    }

    public function cancel(Subscription $subscription): void
    {
        $subscription->update([
            'status' => 'canceled',
            'ends_at' => now(),
        ]);
    }
}
