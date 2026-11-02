<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order/pesanan', function () {
    return view('order.pesanan');
});

Route::get('/order/pesanan', function () {
    return view('order.pesanan');
});

Route::get('/order/delivery', function () {
    return view('order.delivery');
});
