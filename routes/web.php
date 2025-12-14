<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTrackController;
use App\Models\Menu;
use App\Models\Category;

Route::get('/', [MenuController::class, 'all'])->name('all');
Route::get('/menu', [MenuController::class, 'all'])->name('all');

Route::get('/menu/details_order/{id}', [MenuController::class, 'showOrderDetail']);

Route::post('/menu/add-to-cart', [MenuController::class, 'addToCart'])->name('cart.add');
Route::get('/menu/cart', [MenuController::class, 'showCart'])->name('cart.index');
Route::post('/menu/cart/update', [MenuController::class, 'updateCartItem'])->name('cart.update');
Route::delete('/menu/cart/remove/{uuid}', [MenuController::class, 'removeCartItem'])->name('cart.remove');
Route::post('/menu/cart/clear', [MenuController::class, 'clearCart'])->name('cart.clear');

Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.show');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Halaman payment
Route::get('/payment/{order}', [OrderController::class, 'showPaymentPage'])->name('payment.page');

// Success page
 Route::get('/payment-success/{invoice}', [PaymentController::class, 'success'])
        ->name('order.success');
    
Route::get('/receipt/{invoice}', [PaymentController::class, 'downloadReceipt'])
        ->name('download-receipt');

// track pesanan
Route::get('/payment/track/{token}', [PaymentTrackController::class, 'show'])
    ->name('payment.track');

Route::get('/menu/{category}', [MenuController::class, 'all']);

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
// });

require __DIR__.'/auth.php';

