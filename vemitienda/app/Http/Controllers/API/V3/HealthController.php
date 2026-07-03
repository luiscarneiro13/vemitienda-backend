<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Jobs\QueueHealthCheckJob;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HealthController extends Controller
{
    use ApiResponser;

    // Comprueba si el/los worker(s) de colas están procesando jobs realmente,
    // despachando un job "heartbeat" y esperando a que se marque como procesado.
    public function queueStatus()
    {
        $token = (string) Str::uuid();
        $cacheKey = "queue_health_check:{$token}";

        QueueHealthCheckJob::dispatch($token);

        $timeoutSeconds = 5;
        $intervalMicroseconds = 200000; // 0.2s
        $elapsed = 0;

        while ($elapsed < $timeoutSeconds) {
            if (Cache::pull($cacheKey)) {
                return $this->successResponse([
                    'message' => 'La cola está corriendo.',
                    'data' => [
                        'running' => true,
                        'seconds_elapsed' => round($elapsed, 1),
                    ],
                ]);
            }

            usleep($intervalMicroseconds);
            $elapsed += $intervalMicroseconds / 1_000_000;
        }

        return $this->errorResponse([
            'status' => 503,
            'message' => 'La cola no está procesando jobs.',
            'error' => [
                'running' => false,
                'timeout_seconds' => $timeoutSeconds,
                'pending_jobs' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
        ]);
    }
}
