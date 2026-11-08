<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAddressController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// WEB
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about.index')->name('about');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::view('/cart', 'pages.cart.index')->name('cart.index');

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'index')->name('contact');
    Route::post('/contact', 'send')->middleware('throttle:contact')->name('contact.send');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/pages', 'index')->name('pages.index');
    Route::get('/pages/{page:slug}', 'show')->name('pages.show');
});

// API pre JS (web middleware kvôli session/CSRF pre košík; limit "shop-api" v AppServiceProvider)
Route::prefix('api')->name('api.')->middleware('throttle:shop-api')->group(function () {

    Route::prefix('products')->name('products.')->controller(ProductApiController::class)->group(function () {
        Route::get('/search', 'search')->name('search');
        Route::get('/{product:slug}', 'show')->name('show');
    });

    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::post('/update', 'update')->name('update');
        Route::post('/remove', 'remove')->name('remove');
        Route::post('/clear', 'clear')->name('clear');
    });
});

// Guest checkout (available for both guests and authenticated users)
Route::controller(OrderController::class)->group(function () {
    Route::get('/checkout', 'checkout')->name('checkout');
    Route::post('/orders', 'store')->name('orders.store');
    Route::get('/orders/confirmation/{order}', 'confirmation')->name('orders.confirmation');
});

// Prihlásený používateľ
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{order}', 'show')->name('show');
            Route::post('/{order}/cancel', 'cancel')->name('cancel');
            Route::delete('/{order}', 'destroy')->name('destroy');
        });
    });

    Route::resource('addresses', UserAddressController::class)
        ->except(['show'])
        ->names('addresses');

    Route::post('/addresses/{address}/default', [UserAddressController::class, 'setDefault'])
        ->name('addresses.setDefault');
});
