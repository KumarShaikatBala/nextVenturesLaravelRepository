<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware(['auth:sanctum']);
    Route::get('profile', 'profile')->middleware(['auth:sanctum']);
});
Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index');
    Route::post('products', 'store')->middleware(['auth:sanctum', 'admin']);
    Route::get('products/{id}', 'show');
    Route::put('products/{id}', 'update')->middleware(['auth:sanctum', 'admin']);
    Route::delete('products/{id}', 'destroy')->middleware(['auth:sanctum', 'admin']);
});
Route::controller(OrderController::class)->group(function () {
    Route::get('orders', 'index')->middleware(['auth:sanctum']);
    Route::post('orders', 'store')->middleware(['auth:sanctum']);
    Route::get('orders/{id}', 'show')->middleware(['auth:sanctum']);
    Route::put('orders/{id}', 'update')->middleware(['auth:sanctum', 'admin']);
    Route::delete('orders/{id}', 'destroy')->middleware(['auth:sanctum', 'admin']);
});

// Create payment route
Route::post('/pay', [PaymentController::class, 'createPayment']);

// Execute payment (after PayPal redirects back to your site)
Route::get('/payment-success', [PaymentController::class, 'executePayment'])->name('payment.success');

// Cancel payment (if user cancels payment)
Route::get('/payment-failure', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
