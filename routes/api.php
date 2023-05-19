<?php

use App\Http\Controllers\API\V2\SocialLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'API\V1\UserController@login');
    Route::post('register', 'API\V1\UserController@register');
});

Route::group(['prefix' => 'v2'], function () {



    Route::post('login', 'API\V2\UserController@login');
    Route::post('register', 'API\V2\UserController@register');
    Route::post('searchEmail', 'API\V2\UserController@searchEmail')->name('searchEmail');
    Route::post('reset1', 'API\V2\UserController@reset1')->name('reset1');

    Route::get('share/{id_encriptado}', 'API\V2\ShareController@shareAPI');

    Route::group(['middleware' => 'auth:api'], function () {

        Route::group(['prefix' => 'social'], function () {
            Route::post('/login/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback']);
        });

        // Route::get('productos', 'API\V2\ProductosController@index');
        Route::get('planes', 'API\V2\PlanesController@index');
        Route::post('productos/store-user', 'API\V2\ProductosController@storeProductUser');
        Route::get('usuarios', 'API\V2\UserController@index');
        Route::get('user-information', 'API\V2\UserController@userInformation');

        Route::apiResource('categories', 'API\V2\CategoriesController');
        Route::apiResource('products-user', 'API\V2\ProductsController');
        Route::Resource('company-user', 'API\V2\CompaniesController')->except(['create', 'edit', 'update']);
        Route::put('company-user/{id}', 'API\V2\CompaniesController@update');
        Route::post('logout', 'API\V2\UserController@logout');
        Route::post('cancel-account', 'API\V2\UserController@cancelAccount');

        Route::post('storeLogo', 'API\V2\ImagesController@storeLogo');
        Route::post('storeImageProduct/{product_id}', 'API\V2\ImagesController@storeImageProduct');
        Route::post('updateImageProduct/{image_id}', 'API\V2\ImagesController@updateImageProduct');
        Route::delete('deleteImageProduct/{image_id}', 'API\V2\ImagesController@deleteImageProduct');

        Route::post('company/delete', 'API\V2\CompaniesController@destroy');

        Route::get('prueba', 'API\V2\UserController@prueba');

        Route::get('themes', 'API\V2\ThemesController@index');
        Route::get('orders', 'API\V2\OrdersController@index');
        Route::post('updateStatus', 'API\V2\OrdersController@updateStatus');
    });
});
