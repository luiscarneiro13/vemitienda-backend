<?php

use App\Core\Middleware\EnsureBranchSelected;
use App\Core\Middleware\EnsureCompanyIsActive;
use App\Core\Middleware\EnsureModuleEnabled;
use App\Core\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo('/admin/login');

        $middleware->validateCsrfTokens(except: [
            'c/*/track-order',
        ]);

        $middleware->alias([
            'tenant.resolve' => ResolveTenant::class,
            'company.active' => EnsureCompanyIsActive::class,
            'module.enabled' => EnsureModuleEnabled::class,
            'branch.selected' => EnsureBranchSelected::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
