<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin authentication (no locale prefix)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Language switcher
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', function ($locale) {
    $availableLocales = config('app.available_locales', ['en', 'fa']);
    if (in_array($locale, $availableLocales)) {
        session(['locale' => $locale]);
    }
    return redirect()->route('catalog.index', ['locale' => $locale]);
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Admin panel (protected by auth + is_admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show']);
    Route::patch('products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Public storefront (with locale prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('{locale}')->middleware('locale')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
})->where('locale', 'en|fa');