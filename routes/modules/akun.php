<?php

use App\Http\Controllers\Akun\UserController;
use App\Http\Controllers\Akun\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::view('/akun', 'placeholder', ['modul' => 'Akun'])->name('akun.index');

// CRUD pengguna, khusus manajer
Route::middleware('can:kelola-pengguna')->group(function () {
    Route::get('/akun/pengguna', [UserController::class, 'index'])->name('akun.users.index');
    Route::post('/akun/pengguna', [UserController::class, 'store'])->name('akun.users.store');
    Route::put('/akun/pengguna/{user}', [UserController::class, 'update'])->name('akun.users.update');
    Route::delete('/akun/pengguna/{user}', [UserController::class, 'destroy'])->name('akun.users.destroy');
});

Route::middleware('can:kelola-role')->group(function () {
    Route::get('/akun/role', [UserRoleController::class, 'index'])->name('akun.role.index');
    Route::patch('/akun/role/{user}', [UserRoleController::class, 'update'])->name('akun.role.update');
});
