<?php

namespace App\Core\Middleware;

use App\Core\Features\FeatureManager;
use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso en rutas: ->middleware('module.enabled:billing')
 */
class EnsureModuleEnabled
{
    public function __construct(
        private readonly TenantContext $tenant,
        private readonly FeatureManager $features,
    ) {}

    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        $company = $this->tenant->company();

        abort_if(! $company, 403, 'No hay una empresa activa.');
        abort_unless($this->features->enabled($company, $moduleSlug), 403, 'Este módulo no está habilitado para tu empresa.');

        return $next($request);
    }
}
