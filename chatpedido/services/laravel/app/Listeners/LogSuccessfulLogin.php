<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use OwenIt\Auditing\Models\Audit;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Audit::create([
            'user_id' => $user->getKey(),
            'user_type' => $user->getMorphClass(),
            'event' => 'login',
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
