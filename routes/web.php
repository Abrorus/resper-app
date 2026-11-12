<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

require __DIR__.'/auth.php';

Route::middleware('auth')->prefix('dashboard')->group(function () {
    // Sebelumnya semua role diarahkan ke finance.report
    Route::get('/', fn () => redirect()->route(auth()->user()->role->homeRoute()))->name('dashboard');

    require __DIR__.'/modules/akun.php';
    require __DIR__.'/modules/pemesanan.php';
    require __DIR__.'/modules/inventaris.php';
    require __DIR__.'/modules/staff.php';
    require __DIR__.'/modules/keuangan.php';
});