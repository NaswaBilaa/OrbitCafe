<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::post('/midtrans/callback', [PaymentController::class, 'callback']);

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/orders/test', function () {
    return response()->json(['message' => 'API works']);
});
