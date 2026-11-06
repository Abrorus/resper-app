<?php

use App\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

Route::get('/financial-report', [FinanceReportController::class, 'index'])->name('finance.report');