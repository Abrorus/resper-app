<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:kelola-pesanan')->group(function () {
    Route::get('/order/pesanan', [OrderController::class, 'pesanan'])->name('order.pesanan');
    Route::post('/order/pesanan', [OrderController::class, 'store'])->name('order.store');

    Route::get('/order/delivery', [OrderController::class, 'delivery'])->name('order.delivery');

    Route::patch('/order/{order}/proses', [OrderController::class, 'process'])->name('order.process');
    Route::post('/order/simulasi', [OrderController::class, 'simulate'])->name('order.simulate');
    Route::delete('/order/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
});