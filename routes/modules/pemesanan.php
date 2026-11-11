<?php

use Illuminate\Support\Facades\Route;

Route::middleware('can:kelola-pesanan')->group(function () {
    Route::view('/order/pesanan', 'order.pesanan')->name('order.pesanan');
    Route::view('/order/delivery', 'order.delivery')->name('order.delivery');
});