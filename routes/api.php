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

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'API\V1\UserController@logout');
        Route::get('planes', 'API\V1\PlanesController@index');
        Route::get('categorias', 'API\V1\CategoriasController@index');
        Route::get('productos', 'API\V1\ProductosController@index');
        Route::post('productos/store-user', 'API\V1\ProductosController@storeProductUser');
        Route::get('usuarios', 'API\V1\UserController@index');
        Route::get('user-information', 'API\V1\UserController@userInformation');
        Route::post('company-user', 'API\V1\CompaniesController@storeCompanyUser');
    });
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
