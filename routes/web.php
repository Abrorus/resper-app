<?php

use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/finance-report', [FinanceReportController::class, 'index'])->name('finance.report');