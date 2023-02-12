<?php

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
    Route::post('searchEmail', 'API\V1\UserController@searchEmail')->name('searchEmail');
    Route::post('reset1', 'API\V1\UserController@reset1')->name('reset1');

    Route::get('share/{id_encriptado}', 'API\V1\ShareController@shareAPI');

    Route::group(['middleware' => 'auth:api'], function () {
        // Route::get('productos', 'API\V1\ProductosController@index');
        Route::get('planes', 'API\V1\PlanesController@index');
        Route::post('productos/store-user', 'API\V1\ProductosController@storeProductUser');
        Route::get('usuarios', 'API\V1\UserController@index');
        Route::get('user-information', 'API\V1\UserController@userInformation');

        Route::apiResource('categories', 'API\V1\CategoriesController');
        Route::apiResource('products-user', 'API\V1\ProductsController');
        Route::Resource('company-user', 'API\V1\CompaniesController')->except(['create', 'edit', 'update']);
        Route::put('company-user/{id}', 'API\V1\CompaniesController@update');
        Route::post('logout', 'API\V1\UserController@logout');
        Route::post('cancel-account', 'API\V1\UserController@cancelAccount');

        Route::post('storeLogo', 'API\V1\ImagesController@storeLogo');
        Route::post('storeImageProduct/{product_id}', 'API\V1\ImagesController@storeImageProduct');
        Route::post('updateImageProduct/{image_id}', 'API\V1\ImagesController@updateImageProduct');
        Route::delete('deleteImageProduct/{image_id}', 'API\V1\ImagesController@deleteImageProduct');

        Route::get('templates', 'API\V1\TemplateCatalogController@index');
        Route::post('company/delete', 'API\V1\CompaniesController@destroy');

        Route::get('prueba', 'API\V1\UserController@prueba');
    });
});
