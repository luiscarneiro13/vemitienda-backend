<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogCategoryController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CatalogCustomizationController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductOptionController;
use App\Http\Controllers\Api\V1\ProductOptionGroupController;
use App\Http\Controllers\Api\V1\StoreController;
use App\Http\Controllers\Api\V1\StoreMetricsController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Todas las rutas de la versión 1 de la API móvil y web se definen bajo el
| prefijo 'v1'. El control de acceso se basa en Laravel Sanctum y roles de
| Spatie Permission (super_admin, store_owner, store_admin).
|
*/

Route::prefix('v1')->group(function () {

    // Autenticación pública
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    // Rutas protegidas con Sanctum
    Route::middleware('auth:sanctum')->group(function () {

        // Gestión de sesión
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/switch-company', [AuthController::class, 'switchCompany']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Perfil y métricas de la tienda / sucursal activa
        Route::get('/store', [StoreController::class, 'show']);
        Route::put('/store', [StoreController::class, 'update']);
        Route::post('/store/logo', [StoreController::class, 'uploadLogo']);
        Route::delete('/store/logo', [StoreController::class, 'deleteLogo']);
        Route::get('/store/branch', [StoreController::class, 'branch']);
        Route::put('/store/branch', [StoreController::class, 'updateBranch']);
        Route::get('/store/metrics', [StoreMetricsController::class, 'show']);

        // Ciudades disponibles
        Route::get('/cities', [CityController::class, 'index']);

        // Catálogos y Personalización de plantilla
        Route::get('/catalogs', [CatalogController::class, 'index']);
        Route::get('/catalogs/{catalog}', [CatalogController::class, 'show']);
        Route::put('/catalogs/{catalog}', [CatalogController::class, 'update']);
        Route::get('/catalog-customization', [CatalogCustomizationController::class, 'getCustomization']);
        Route::put('/catalog-customization', [CatalogCustomizationController::class, 'updateCustomization']);
        Route::get('/catalog-customization/templates', [CatalogCustomizationController::class, 'templates']);

        // Categorías de catálogo
        Route::get('/categories', [CatalogCategoryController::class, 'index']);
        Route::post('/categories', [CatalogCategoryController::class, 'store']);
        Route::post('/categories/reorder', [CatalogCategoryController::class, 'reorder']);
        Route::get('/categories/icon-suggestions', [CatalogCategoryController::class, 'iconSuggestions']);
        Route::get('/categories/{catalogCategory}', [CatalogCategoryController::class, 'show']);
        Route::put('/categories/{catalogCategory}', [CatalogCategoryController::class, 'update']);
        Route::patch('/categories/{catalogCategory}/toggle-active', [CatalogCategoryController::class, 'toggleActive']);
        Route::delete('/categories/{catalogCategory}', [CatalogCategoryController::class, 'destroy']);

        // Productos
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/reorder', [ProductController::class, 'reorder']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggleActive']);
        Route::patch('/products/{product}/toggle-available', [ProductController::class, 'toggleAvailable']);
        Route::post('/products/{product}/image', [ProductController::class, 'uploadImage']);
        Route::delete('/products/{product}/image', [ProductController::class, 'deleteImage']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Grupos de opciones de Producto
        Route::get('/products/{product}/option-groups', [ProductOptionGroupController::class, 'index']);
        Route::post('/products/{product}/option-groups', [ProductOptionGroupController::class, 'store']);
        Route::post('/products/{product}/option-groups/reorder', [ProductOptionGroupController::class, 'reorder']);
        Route::get('/option-groups/{optionGroup}', [ProductOptionGroupController::class, 'show']);
        Route::put('/option-groups/{optionGroup}', [ProductOptionGroupController::class, 'update']);
        Route::delete('/option-groups/{optionGroup}', [ProductOptionGroupController::class, 'destroy']);

        // Opciones individuales
        Route::post('/option-groups/{optionGroup}/options', [ProductOptionController::class, 'store']);
        Route::post('/option-groups/{optionGroup}/options/reorder', [ProductOptionController::class, 'reorder']);
        Route::put('/options/{option}', [ProductOptionController::class, 'update']);
        Route::patch('/options/{option}/toggle-active', [ProductOptionController::class, 'toggleActive']);
        Route::delete('/options/{option}', [ProductOptionController::class, 'destroy']);

        // Equipo de la tienda
        Route::get('/team', [TeamController::class, 'index']);
        Route::post('/team', [TeamController::class, 'store']);
        Route::get('/team/{user}', [TeamController::class, 'show']);
        Route::put('/team/{user}', [TeamController::class, 'update']);
        Route::delete('/team/{user}', [TeamController::class, 'destroy']);

        // Rutas legacy de starter
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::apiResource('companies', CompanyController::class)->only([
            'index', 'show', 'store', 'update',
        ]);
    });
});
