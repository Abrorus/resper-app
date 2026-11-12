<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:kelola-inventaris')->group(function () {
    Route::get('/inventaris', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventaris', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventaris/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventaris/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventaris/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
});