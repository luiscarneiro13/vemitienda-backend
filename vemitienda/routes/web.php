<?php

use App\Events\InicioDescarga;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', 'NewHomeController@index')->name('home');
Route::get('/catalogo/{slug}', 'WEB\V3\ShareController@index');
Route::get('/pruebas', 'PruebasController@index')->name('pruebas');
Route::get('/nuevas_pruebas', 'PruebasController@index')->name('nuevas_pruebas');
Route::get('/changeFormat', 'PruebasController@changeFormat')->name('changeFormat');
Route::get('politica', fn () => view('politica'));
Route::get('privacidad', fn () => view('privacidad'));
Route::get('ejemplo', fn () => view('ejemplo'));
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// Blog y mensajería
Route::get('blog', 'BlogController@index')->name('blog');
Route::get('blog/{slug}', 'BlogController@show')->name('blog.show');
Route::get('message', 'API\V3\UserController@message')->name('message');
Route::get('confirmationuser/{id_encriptado}', 'API\V3\UserController@confirmationuser');
Route::get('reset2/{user_id}', 'API\V3\UserController@reset2')->name('reset2');
Route::post('reset3', 'API\V3\UserController@reset3')->name('reset3');

// Autenticación y administración
Route::get('login', 'Admin\LoginController@login')->name('login');
Route::post('loguear', 'Admin\LoginController@loguear')->name('loguear');
Route::post('logout', 'Admin\LoginController@logout')->name('logout');

// Rutas de catálogo y productos (al final porque /{slug} es comodín)
Route::post('cart', 'WEB\V3\CartController@addToCart')->name('cart.store');
Route::post('update-cart', 'WEB\V3\CartController@updateCart')->name('cart.update');
Route::post('remove', 'WEB\V3\CartController@removeCart')->name('cart.remove');
Route::post('clear', 'WEB\V3\CartController@clearAllCart')->name('cart.clear');
Route::post('order', 'WEB\V3\OrderController@index')->name('order.store');
Route::get('/{slug}/cart', 'WEB\V3\CartController@cartList')->name('cart.list');
Route::get('/{slug}', 'WEB\V3\ProductController@productList')->name('products.list');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::resource('usuarios', 'Admin\UsersController');
    Route::resource('blog', 'Admin\PostsController');
    Route::post('blog-ai/generate', 'Admin\PostAiGenerationController@generate')->name('blog.ai.generate');
    Route::get('blog-ai/status/{generation}', 'Admin\PostAiGenerationController@status')->name('blog.ai.status');
    Route::get('blog-templates', 'Admin\PostTemplatesController@index')->name('blog.templates.index');
    Route::post('blog-templates', 'Admin\PostTemplatesController@store')->name('blog.templates.store');
    Route::put('blog-templates/{template}', 'Admin\PostTemplatesController@update')->name('blog.templates.update');
    Route::delete('blog-templates/{template}', 'Admin\PostTemplatesController@destroy')->name('blog.templates.destroy');
    Route::resource('plans', 'Admin\PlansController');
    Route::resource('versions', 'Admin\VersionsController');
    Route::resource('tags', 'Admin\TagsController');
    Route::resource('postcategory', 'Admin\PostCategoryController');
    Route::resource('planusers', 'Admin\PlanUsersController');
    Route::resource('paymentmethods', 'Admin\PaymentMethodsController');
    Route::resource('payments', 'Admin\PaymentsController');
    Route::post('storeImagePost', 'API\V3\ImagesController@storeImagePost')->name("upload");
});

// Otros servicios y eventos
Route::post('contact', 'API\V3\ContactController@index')->name('contact');
Route::get('/init/{id_usuario}', 'API\V3\ShareController@init');
Route::get('/share/{id_encriptado}', 'API\V3\ShareController@share');

Route::get('prueba', 'WEB\V3\OrderController@prueba');
Route::get('contacto', 'ContactoController@index')->name('contacto');
Route::get('delete-account', 'ContactoController@deleteAccount')->name('deleteAccount');

Route::get('/enviar', function () {
    info("Luis: se va a emitir el evento");
    event(new InicioDescarga("Hola desde Laravel + Ably!"));
    return "Mensaje enviado";
});
