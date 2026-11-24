<?php

namespace App\Core\Middleware;

use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Para módulos donde toda operación necesita una sucursal activa
 * concreta (p. ej. inventario, punto de venta).
 */
class EnsureBranchSelected
{
    public function __construct(private readonly TenantContext $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_if(! $this->tenant->branch(), 428, 'Debes seleccionar una sucursal antes de continuar.');

        return $next($request);
    }
}
