<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use OwenIt\Auditing\Models\Audit;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        /** @var User|null $user */
        $user = $event->user;

        if (! $user) {
            return;
        }

        Audit::create([
            'user_id' => $user->getKey(),
            'user_type' => $user->getMorphClass(),
            'event' => 'logout',
            'auditable_id' => $user->getKey(),
            'auditable_type' => $user->getMorphClass(),
            'old_values' => [],
            'new_values' => [],
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => 'auth',
        ]);
    }
}
