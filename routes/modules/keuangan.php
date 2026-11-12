<?php

use App\Http\Controllers\Finance\ExpenseTransactionController;
use App\Http\Controllers\Finance\SalesTransactionController;
use App\Http\Controllers\Finance\TransactionLogController;
use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

// Keuangan-3: Melihat Laporan Keuangan
Route::get('/financial-report', [FinanceReportController::class, 'index'])->name('finance.report');

Route::prefix('keuangan')->name('finance.')->group(function () {
    // Keuangan-1: Mencatat Transaksi Penjualan
    Route::get('/penjualan', [SalesTransactionController::class, 'index'])->name('sales.index');
    Route::post('/penjualan', [SalesTransactionController::class, 'store'])->name('sales.store');
    Route::get('/penjualan/{sale}', [SalesTransactionController::class, 'show'])->name('sales.show');
    Route::get('/penjualan/{sale}/edit', [SalesTransactionController::class, 'edit'])->name('sales.edit');
    Route::put('/penjualan/{sale}', [SalesTransactionController::class, 'update'])->name('sales.update');
    Route::delete('/penjualan/{sale}', [SalesTransactionController::class, 'destroy'])->name('sales.destroy');

    // Keuangan-2: Mencatat Transaksi Pengeluaran (CRUD lengkap)
    Route::resource('pengeluaran', ExpenseTransactionController::class)
        ->except('show')
        ->parameters(['pengeluaran' => 'expense'])
        ->names('expenses');

    // Keuangan-4: Mengoreksi Kesalahan Input Transaksi (riwayat koreksi)
    Route::get('/koreksi', [TransactionLogController::class, 'index'])->name('corrections.index');
});
