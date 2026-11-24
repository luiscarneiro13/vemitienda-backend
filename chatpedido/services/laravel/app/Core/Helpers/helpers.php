<?php

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Features\FeatureManager;
use App\Core\Media\MediaManager;
use App\Core\Modules\Models\Module;
use App\Core\Modules\ModuleManager;
use App\Core\Settings\SettingsManager;
use App\Core\Tenancy\TenantContext;
use App\Core\Timeline\Models\TimelineEntry;
use App\Core\Timeline\TimelineService;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('tenant')) {
    /**
     * Única puerta de entrada a "quién es la empresa/sucursal/usuario
     * actuales". Ver App\Core\Tenancy\TenantContext.
     *
     * Uso: tenant()->company(), tenant()->branch(), tenant()->user(),
     * tenant()->companyId(), tenant()->branchId(), tenant()->userId().
     */
    function tenant(): TenantContext
    {
        return app(TenantContext::class);
    }
}

if (! function_exists('company')) {
    /**
     * Atajo a tenant()->company().
     */
    function company(): ?Company
    {
        return tenant()->company();
    }
}

if (! function_exists('branch')) {
    /**
     * Atajo a tenant()->branch().
     */
    function branch(): ?Branch
    {
        return tenant()->branch();
    }
}

if (! function_exists('setting')) {
    /**
     * setting() devuelve el SettingsManager; setting('key', $default) lee
     * un valor con casting automático según el tipo declarado.
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        $settings = app(SettingsManager::class);

        return $key === null ? $settings : $settings->get($key, $default);
    }
}

if (! function_exists('module')) {
    /**
     * module() devuelve el ModuleManager; module('slug') devuelve el
     * módulo del catálogo global (o null si no está registrado).
     */
    function module(?string $slug = null): ModuleManager|Module|null
    {
        $modules = app(ModuleManager::class);

        return $slug === null ? $modules : $modules->list()->firstWhere('slug', $slug);
    }
}

if (! function_exists('feature')) {
    /**
     * feature() devuelve el FeatureManager; feature('slug') comprueba si
     * el módulo está habilitado para la empresa activa del TenantContext.
     */
    function feature(?string $moduleSlug = null): FeatureManager|bool
    {
        $features = app(FeatureManager::class);

        if ($moduleSlug === null) {
            return $features;
        }

        $currentCompany = company();

        return $currentCompany !== null && $features->enabled($currentCompany, $moduleSlug);
    }
}

if (! function_exists('timeline')) {
    /**
     * Registra una entrada en el timeline funcional. Ver
     * App\Core\Timeline\TimelineService::log().
     */
    function timeline(?string $action = null, ?string $description = null, ?Model $subject = null, array $properties = []): TimelineService|TimelineEntry
    {
        $service = app(TimelineService::class);

        if ($action === null || $description === null) {
            return $service;
        }

        return $service->log($action, $description, $subject, $properties);
    }
}

if (! function_exists('media')) {
    /**
     * Devuelve el MediaManager (attach/delete/url/for). Ver
     * App\Core\Media\MediaManager.
     */
    function media(): MediaManager
    {
        return app(MediaManager::class);
    }
}
