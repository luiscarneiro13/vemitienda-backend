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

// Rutas públicas
Route::get('/', 'NewHomeController@index')->name('home');
Route::get('/pruebas', 'PruebasController@index')->name('pruebas');
Route::get('/changeFormat', 'PruebasController@changeFormat')->name('changeFormat');
Route::get('politica', fn () => view('politica'));
Route::get('privacidad', fn () => view('privacidad'));
Route::get('ejemplo', fn () => view('ejemplo'));
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// Rutas de catálogo y productos
Route::get('/catalogo/{slug}', 'WEB\V3\ShareController@index');
Route::get('/{slug}', [ProductController3::class, 'productList'])->name('products.list');
Route::get('/{slug}/cart', [CartController3::class, 'cartList'])->name('cart.list');
Route::post('cart', [CartController3::class, 'addToCart'])->name('cart.store');
Route::post('update-cart', [CartController3::class, 'updateCart'])->name('cart.update');
Route::post('remove', [CartController3::class, 'removeCart'])->name('cart.remove');
Route::post('clear', [CartController3::class, 'clearAllCart'])->name('cart.clear');
Route::post('order', [OrderController3::class, 'index'])->name('order.store');

// Descarga de videos
Route::prefix('descargar/videos')->group(function () {
    Route::get('/', [DownloaderController::class, 'index'])->name('home');
    Route::post('/prepare', [DownloaderController::class, 'prepare'])->name('prepare');
    Route::get('/status/{video}', [DownloaderController::class, 'status'])->name('status');
    Route::get('/download/{video}', [DownloaderController::class, 'download'])->name('download');
});

// Blog y mensajería
Route::get('blog', [BlogController::class, 'index'])->name('blog');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog');
Route::get('message', 'API\V3\UserController@message')->name('message');
Route::get('confirmationuser/{id_encriptado}', 'API\V3\UserController@confirmationuser');
Route::get('reset2/{user_id}', 'API\V3\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V3\UserController@reset3')->name('reset3');

// Autenticación y administración
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/loguear', [LoginController::class, 'loguear'])->name('loguear');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::resource('usuarios', AdminUsersController::class);
    Route::resource('blog', AdminPostsController::class);
    Route::resource('plans', AdminPlansController::class);
    Route::resource('versions', AdminVersionsController::class);
    Route::resource('tags', AdminTagsController::class);
    Route::resource('postcategory', AdminPostCategoryController::class);
    Route::resource('planusers', AdminPlanUsersController::class);
    Route::resource('paymentmethods', AdminPaymentMethodsController::class);
    Route::resource('payments', AdminPaymentsController::class);
    Route::post('storeImagePost', 'API\V3\ImagesController@storeImagePost')->name("upload");
});

// Otros servicios y eventos
Route::post('contact', 'API\V3\ContactController@index')->name('contact');
Route::get('/init/{id_usuario}', 'API\V3\ShareController@init');
Route::get('/share/{id_encriptado}', 'API\V3\ShareController@share');

Route::get('prueba', [OrderController3::class, 'prueba']);
Route::get('contacto', 'ContactoController@index')->name('contacto');
Route::get('delete-account', 'ContactoController@deleteAccount')->name('deleteAccount');

Route::get('/enviar', function () {
    info("Luis: se va a emitir el evento");
    event(new InicioDescarga("Hola desde Laravel + Ably!"));
    return "Mensaje enviado";
});
