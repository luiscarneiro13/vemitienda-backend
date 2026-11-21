<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicCatalog\PublicCatalogController;
use App\Http\Controllers\PublicCatalog\StoreDirectoryController;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', StoreDirectoryController::class)->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/profile/api-tokens/{tokenId}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
});

if (class_exists(Scramble::class)) {
    Scramble::registerUiRoute('docs')->name('scramble.docs.ui');
    Scramble::registerJsonSpecificationRoute('docs.json')->name('scramble.docs.document');
}

if (class_exists(Scramble::class)) {
    Scramble::registerUiRoute('docs')->name('scramble.docs.ui');
    Scramble::registerJsonSpecificationRoute('docs.json')->name('scramble.docs.document');
}

Route::prefix('c')->name('public.catalog.')->middleware('throttle:public-catalog')->group(function () {
    Route::post('{companySlug}/{branchUuid}/track-order', [PublicCatalogController::class, 'trackOrder'])
        ->middleware('throttle:public-catalog-write')
        ->where('companySlug', '[a-z0-9\-]+')
        ->whereUuid('branchUuid')
        ->name('track_order');

    Route::get('{companySlug}/{branchUuid}', [PublicCatalogController::class, 'show'])
        ->where('companySlug', '[a-z0-9\-]+')
        ->whereUuid('branchUuid')
        ->name('show');

    // Cualquier otra forma (sin UUID, UUID inválido, segmentos extra) => error
    Route::get('{any?}', [PublicCatalogController::class, 'invalid'])
        ->where('any', '.*')
        ->name('invalid');
});

require __DIR__.'/auth.php';
