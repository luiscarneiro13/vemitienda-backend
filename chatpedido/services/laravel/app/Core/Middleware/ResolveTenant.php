<?php

namespace App\Core\Middleware;

use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fuerza la resolución temprana del tenant en el ciclo de vida de la
 * request. No bloquea si no hay empresa activa — para eso está
 * EnsureCompanyIsActive.
 */
class ResolveTenant
{
    public function __construct(private readonly TenantContext $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->tenant->company();

        return $next($request);
    }
}
