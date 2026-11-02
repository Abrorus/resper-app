<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::get('/finance-report', [FinanceReportController::class, 'index'])->name('finance.report');