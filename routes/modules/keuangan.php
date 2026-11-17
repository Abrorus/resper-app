<?php

use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

Route::get('/financial-report', [FinanceReportController::class, 'index'])
    ->middleware('can:lihat-laporan-keuangan')
    ->name('finance.report');

// Nanti untuk kasir (Keuangan-1 dan Keuangan-2), buat route terpisah, misalnya:
// Route::middleware('can:catat-transaksi')->group(function () { ... });