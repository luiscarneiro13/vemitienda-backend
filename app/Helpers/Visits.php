<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Visits
{
    public function index($request)
    {
        info("Entró antes de registrar");
        $ip_address = $request->ip();
        $user_agent = $request->header('User-Agent');
        $referer = $request->header('Referer');

        $visit = DB::table('visits')->where('ip_address', $ip_address)->first();

        if (!$visit) {
            info('Registró la visita');
            DB::table('visits')->insert([
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'referer' => $referer,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
