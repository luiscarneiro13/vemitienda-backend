<?php

use App\Http\Controllers\WEB\V2\CartController;
use App\Http\Controllers\WEB\V2\OrderController;
use App\Http\Controllers\WEB\V2\ProductController;
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
})->name('home');

Route::get('/newWelcome', function () {
    return view('newWelcome');
})->name('newHome');

// Auth::routes();

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('init/{id_usuario}', 'API\V1\ShareController@init');
Route::get('share/{id_encriptado}', 'API\V1\ShareController@share');
Route::get('confirmationuser/{id_encriptado}', 'API\V1\UserController@confirmationuser');
Route::get('message', 'API\V1\UserController@message')->name('message');

Route::get('reset2/{user_id}', 'API\V1\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V1\UserController@reset3')->name('reset3');

Route::post('contact', 'API\V1\ContactController@index')->name('contact');

Route::get('contacto',  function () {
    return view('contacto');
})->name('contacto');

Route::get('prueba', function () {
    return view('auth.resetPassword');
});


Route::get('/{slug}', [ProductController::class, 'productList'])->name('products.list');
Route::get('/{slug}/cart', [CartController::class, 'cartList'])->name('cart.list');
Route::post('cart', [CartController::class, 'addToCart'])->name('cart.store');
Route::post('update-cart', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('remove', [CartController::class, 'removeCart'])->name('cart.remove');
Route::post('clear', [CartController::class, 'clearAllCart'])->name('cart.clear');
Route::post('order', [OrderController::class, 'index'])->name('order.store');
Route::get('prueba', [OrderController::class, 'prueba']);
