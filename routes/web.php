<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PlatoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Commerce de Mangas (MANGA・VAULT)
|--------------------------------------------------------------------------
*/

// Catálogo y Ficha de Manga
Route::get('/', [MangaController::class, 'index'])->name('home');
Route::get('/mangas', [MangaController::class, 'index'])->name('mangas.index');
Route::get('/mangas/{slug}', [MangaController::class, 'show'])->name('mangas.show');

// Carrito de Compras (Session Based)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{manga}', [CartController::class, 'add'])->name('add');
    Route::put('/update/{manga}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{manga}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Checkout y Procesamiento de Órdenes (Service Layer)
Route::prefix('order-checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| Módulos Anteriores (Compatibilidad)
|--------------------------------------------------------------------------
*/
Route::controller(PlatoController::class)->group(function () {
    Route::get('platos', 'index')->name('platos.index');
    Route::get('platos/crear', 'create')->name('platos.create');
    Route::post('platos', 'store')->name('platos.store');
    Route::get('platos/{plato}/editar', 'edit')->name('platos.edit');
    Route::put('platos/{plato}', 'update')->name('platos.update');
    Route::delete('platos/{plato}', 'destroy')->name('platos.destroy');
});

Route::controller(MenuController::class)->group(function () {
    Route::get('menu', 'index')->name('menu.index');
    Route::get('menu/{plato}', 'show')->name('menu.show');
});

Route::get('/email', [EmailController::class, 'index'])->name('email.index');
Route::post('/email', [EmailController::class, 'send'])->name('email.send');

Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);
