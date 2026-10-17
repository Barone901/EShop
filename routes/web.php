<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// WEB
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// API pre JS (web middleware kvôli session/CSRF pre košík)
Route::prefix('api')->name('api.')->group(function () {

    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::post('/update', 'update')->name('update');
        Route::post('/remove', 'remove')->name('remove');
        Route::post('/clear', 'clear')->name('clear');
    });
});

// Prihlásený používateľ
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});
