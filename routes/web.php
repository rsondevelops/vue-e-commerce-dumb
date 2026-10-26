<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\ProductCatalogController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




Route::get('/laravel', function () {
    return Inertia::render('Laravel');
});

Route::get('/about', function () {
    return Inertia::render('About');
});





























Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/beranda', [HomeController::class, 'home'])->name('home');

Route::get('/produk', [ProductCatalogController::class, 'index'])->name('products.catalog');
Route::get('/produk/{slug}', [ProductCatalogController::class, 'show'])->name('products.show');

Route::get('/keranjang', function () {
    return Inertia::render('Public/Cart/Index');
})->name('cart');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pesanan/{orderCode}/sukses', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/pesanan-saya', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/pesanan-saya/{orderCode}', [OrderHistoryController::class, 'show'])->name('orders.show');
});

Route::get('/playground', function () {
    return Inertia::render('Playground');
})->name('playground');

// ================================================
// AUTENTIKASI
// ================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ================================================
// ADMIN — sekarang diproteksi GANDA: auth + admin
// ================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)
        ->except(['create', 'edit', 'show'])
        ->names('categories');


    Route::resource('products', ProductController::class)
        ->except(['show'])
        ->names('products');
    Route::get('/products-trashed', [ProductController::class, 'trashed'])
        ->name('products.trashed');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])
        ->name('products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])
        ->name('products.force-delete');
    
    
    Route::resource('banners', BannerController::class)
        ->except(['create', 'edit', 'show'])
        ->names('banners');
    Route::post('/banners/{banner}/move-up', [BannerController::class, 'moveUp'])
        ->name('banners.move-up');
    Route::post('/banners/{banner}/move-down', [BannerController::class, 'moveDown'])
        ->name('banners.move-down');


    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});