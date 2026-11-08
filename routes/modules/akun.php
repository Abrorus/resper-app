<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/akun', 'placeholder', ['modul' => 'Akun'])->name('akun.index');