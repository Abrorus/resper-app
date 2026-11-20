<?php

use App\Http\Controllers\Finance\ExpenseTransactionController;
use App\Http\Controllers\Finance\SalesTransactionController;
use App\Http\Controllers\Finance\TransactionLogController;
use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

// Keuangan-3: Melihat Laporan Keuangan (Manajer)
Route::get('/financial-report', [FinanceReportController::class, 'index'])
    ->middleware('can:lihat-laporan-keuangan')
    ->name('finance.report');

Route::prefix('keuangan')->name('finance.')->group(function () {
    // Keuangan-1 dan Keuangan-2: Kasir mencatat transaksi
    Route::middleware('can:catat-transaksi')->group(function () {
        Route::get('/penjualan', [SalesTransactionController::class, 'index'])->name('sales.index');
        Route::post('/penjualan', [SalesTransactionController::class, 'store'])->name('sales.store');
        Route::get('/penjualan/{sale}', [SalesTransactionController::class, 'show'])->name('sales.show');

        Route::get('/pengeluaran', [ExpenseTransactionController::class, 'index'])->name('expenses.index');
        Route::get('/pengeluaran/create', [ExpenseTransactionController::class, 'create'])->name('expenses.create');
        Route::post('/pengeluaran', [ExpenseTransactionController::class, 'store'])->name('expenses.store');
    });

    // Keuangan-4: Manajer mengoreksi kesalahan input transaksi
    Route::middleware('can:koreksi-transaksi')->group(function () {
        Route::get('/penjualan/{sale}/edit', [SalesTransactionController::class, 'edit'])->name('sales.edit');
        Route::put('/penjualan/{sale}', [SalesTransactionController::class, 'update'])->name('sales.update');
        Route::delete('/penjualan/{sale}', [SalesTransactionController::class, 'destroy'])->name('sales.destroy');

        Route::get('/pengeluaran/{expense}/edit', [ExpenseTransactionController::class, 'edit'])->name('expenses.edit');
        Route::put('/pengeluaran/{expense}', [ExpenseTransactionController::class, 'update'])->name('expenses.update');
        Route::delete('/pengeluaran/{expense}', [ExpenseTransactionController::class, 'destroy'])->name('expenses.destroy');

        Route::get('/koreksi', [TransactionLogController::class, 'index'])->name('corrections.index');
    });
});
