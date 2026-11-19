<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
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
// Storefront languages: English and Farsi only (Arabic is used by the dashboard)
Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);
    return redirect()->route('catalog.index', ['locale' => $locale]);
})->where('locale', 'en|fa')->name('lang.switch');

// Old Arabic storefront links go to English
Route::redirect('/ar', '/en');

/*
|--------------------------------------------------------------------------
| Admin panel (protected by auth + is_admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin', 'admin_locale'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard language switcher (English / Arabic)
    Route::get('lang/{locale}', function ($locale) {
        session(['admin_locale' => $locale]);
        return back();
    })->where('locale', 'en|ar')->name('lang');

    Route::resource('products', ProductController::class)->except(['show']);
    Route::patch('products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

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
Route::redirect('/', '/en');
Route::prefix('{locale}')->where(['locale' => 'en|fa'])->middleware('locale')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/cart', [CatalogController::class, 'cart'])->name('catalog.cart');
    Route::post('/cart/order', [CatalogController::class, 'storeOrder'])
        ->middleware('throttle:20,1')->name('catalog.cart.order');
    Route::post('/cart/pdf', [CatalogController::class, 'uploadOrderPdf'])
        ->middleware('throttle:10,1')->name('catalog.cart.pdf');
    Route::post('/cart/send', [CatalogController::class, 'sendOrder'])
        ->middleware('throttle:10,1')->name('catalog.cart.send');
});