<?php

namespace App\Core\Middleware;

use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function __construct(private readonly TenantContext $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $company = $this->tenant->company();

        abort_if($company && ! $company->is_active, 403, 'Esta empresa está desactivada.');

        return $next($request);
    }
}
