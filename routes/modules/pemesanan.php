<?php

use Illuminate\Support\Facades\Route;

Route::get('/order/pesanan', function () {
    return view('order.pesanan');
})->name('order.pesanan');

Route::get('/order/delivery', function () {
    return view('order.delivery');
})->name('order.delivery');
