<?php

use Illuminate\Support\Facades\Route;

Route::view('/order/pesanan', 'placeholder', ['modul' => 'Pemesanan', 'judul' => 'Pesanan'])->name('order.pesanan');
Route::view('/order/delivery', 'placeholder', ['modul' => 'Pemesanan', 'judul' => 'Delivery Order'])->name('order.delivery');