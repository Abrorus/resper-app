<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::get('/inventaris', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventaris', [InventoryController::class, 'store'])->name('inventory.store');
Route::get('/inventaris/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
Route::put('/inventaris/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
Route::delete('/inventaris/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
