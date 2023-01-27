<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('init/{id_usuario}', 'API\V1\ShareController@init');
Route::get('share/{id_encriptado}', 'API\V1\ShareController@share');
Route::get('confirmationuser/{id_encriptado}', 'API\V1\UserController@confirmationuser');
Route::get('message', 'API\V1\UserController@message')->name('message');

Route::get('reset2/{user_id}', 'API\V1\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V1\UserController@reset3')->name('reset3');

Route::get('prueba', function () {
    return view('auth.resetPassword');
});
