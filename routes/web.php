<?php

use App\Http\Controllers\WEB\V3\CartController as CartController3;
use App\Http\Controllers\WEB\V3\OrderController as OrderController3;
use App\Http\Controllers\WEB\V3\ProductController as ProductController3;
use App\Http\Controllers\Admin\LoginController;
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


// Auth::routes();
Route::get('/', 'NewHomeController@index')->name('home');
Route::get('/pruebas', 'PruebasController@index')->name('pruebas');
Route::get('/changeFormat', 'PruebasController@changeFormat')->name('changeFormat');
Route::get('politica', function () {
    return view('politica');
});
Route::get('privacidad', function () {
    return view('privacidad');
});
Route::get('ejemplo', function () {
    return view('ejemplo');
});
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// BACK OFFICE
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/loguear', [LoginController::class, 'loguear'])->name('loguear');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::resource('usuarios', Admin\UsersController::class);
    Route::resource('plans', Admin\PlansController::class);
    Route::resource('planusers', Admin\PlanUsersController::class);
    Route::resource('paymentmethods', Admin\PaymentMethodsController::class);
    Route::resource('payments', Admin\PaymentsController::class);
});

Route::get('init/{id_usuario}', 'API\V3\ShareController@init');
Route::get('share/{id_encriptado}', 'API\V3\ShareController@share');
Route::get('catalogo/{slug}', 'WEB\V3\ShareController@index');
Route::get('confirmationuser/{id_encriptado}', 'API\V3\UserController@confirmationuser');
Route::get('message', 'API\V3\UserController@message')->name('message');

Route::get('reset2/{user_id}', 'API\V3\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V3\UserController@reset3')->name('reset3');

Route::post('contact', 'API\V3\ContactController@index')->name('contact');

Route::get('prueba', [OrderController3::class, 'prueba']);
Route::get('contacto', 'ContactoController@index')->name('contacto');

Route::get('/{slug}', [ProductController3::class, 'productList'])->name('products.list');
Route::get('/{slug}/cart', [CartController3::class, 'cartList'])->name('cart.list');
Route::post('cart', [CartController3::class, 'addToCart'])->name('cart.store');
Route::post('update-cart', [CartController3::class, 'updateCart'])->name('cart.update');
Route::post('remove', [CartController3::class, 'removeCart'])->name('cart.remove');
Route::post('clear', [CartController3::class, 'clearAllCart'])->name('cart.clear');
Route::post('order', [OrderController3::class, 'index'])->name('order.store');
