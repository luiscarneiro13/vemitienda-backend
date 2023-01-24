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
Route::get('confirmated_user', 'API\V1\UserController@confirmatedUser');
Route::get('prueba', 'API\V1\UserController@confirmatedUser');
