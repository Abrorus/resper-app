<?php

use App\Http\Controllers\Akun\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::view('/akun', 'placeholder', ['modul' => 'Akun'])->name('akun.index');

Route::middleware('can:kelola-role')->group(function () {
    Route::get('/akun/role', [UserRoleController::class, 'index'])->name('akun.role.index');
    Route::patch('/akun/role/{user}', [UserRoleController::class, 'update'])->name('akun.role.update');
});