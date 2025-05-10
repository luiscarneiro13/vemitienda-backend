<?php

use App\Events\InicioDescarga;
use App\Http\Controllers\Admin\LoginController;

use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\PostsController as AdminPostsController;
use App\Http\Controllers\Admin\PlansController as AdminPlansController;
use App\Http\Controllers\Admin\VersionsController as AdminVersionsController;
use App\Http\Controllers\Admin\TagsController as AdminTagsController;
use App\Http\Controllers\Admin\PostCategoryController as AdminPostCategoryController;
use App\Http\Controllers\Admin\PlanUsersController as AdminPlanUsersController;
use App\Http\Controllers\Admin\PaymentMethodsController as AdminPaymentMethodsController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DownloaderController;
use App\Http\Controllers\WEB\V3\CartController as CartController3;
use App\Http\Controllers\WEB\V3\OrderController as OrderController3;
use App\Http\Controllers\WEB\V3\ProductController as ProductController3;
use Illuminate\Support\Facades\Route;

use App\Events\MensajeNuevo;


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

Route::get('catalogo/{slug}', 'WEB\V3\ShareController@index');

Route::prefix('descargar/videos')->group(function () {
    Route::get('/', [DownloaderController::class, 'index'])->name('home');
    Route::post('/prepare', [DownloaderController::class, 'prepare'])->name('prepare');
    Route::get('/status/{video}', [DownloaderController::class, 'status'])->name('status');
    Route::get('/download/{video}', [DownloaderController::class, 'download'])->name('download');
});

// BACK OFFICE
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('/loguear', [LoginController::class, 'loguear'])->name('loguear');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('blog', [BlogController::class, 'index'])->name('blog');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::resource('usuarios', Admin\UsersController::class);
    Route::resource('blog', Admin\PostsController::class);
    Route::resource('plans', Admin\PlansController::class);
    Route::resource('versions', Admin\VersionsController::class);
    Route::resource('tags', Admin\TagsController::class);
    Route::resource('postcategory', Admin\PostCategoryController::class);
    Route::resource('planusers', Admin\PlanUsersController::class);
    Route::resource('paymentmethods', Admin\PaymentMethodsController::class);
    Route::resource('payments', Admin\PaymentsController::class);
    // Agregar imágenes al crear posts desde el admin
    Route::post('storeImagePost', 'API\V3\ImagesController@storeImagePost')->name("upload");
});

Route::get('init/{id_usuario}', 'API\V3\ShareController@init');
Route::get('share/{id_encriptado}', 'API\V3\ShareController@share');
Route::get('confirmationuser/{id_encriptado}', 'API\V3\UserController@confirmationuser');
Route::get('message', 'API\V3\UserController@message')->name('message');

Route::get('reset2/{user_id}', 'API\V3\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V3\UserController@reset3')->name('reset3');

Route::post('contact', 'API\V3\ContactController@index')->name('contact');

Route::get('prueba', [OrderController3::class, 'prueba']);
Route::get('contacto', 'ContactoController@index')->name('contacto');
Route::get('delete-account', 'ContactoController@deleteAccount')->name('deleteAccount');


Route::get('/enviar', function () {
    info("Luis: se va a emitir el evento");
    event(new InicioDescarga("Hola desde Laravel + Ably!"));
    return "Mensaje enviado";
});




Route::get('/{slug}', [ProductController3::class, 'productList'])->name('products.list');
Route::get('/{slug}/cart', [CartController3::class, 'cartList'])->name('cart.list');
Route::post('cart', [CartController3::class, 'addToCart'])->name('cart.store');
Route::post('update-cart', [CartController3::class, 'updateCart'])->name('cart.update');
Route::post('remove', [CartController3::class, 'removeCart'])->name('cart.remove');
Route::post('clear', [CartController3::class, 'clearAllCart'])->name('cart.clear');
Route::post('order', [OrderController3::class, 'index'])->name('order.store');
