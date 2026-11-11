<?php

use App\Http\Controllers\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/staff', [StaffController::class, 'index'])
    ->middleware('can:kelola-staf')
    ->name('staff.index');

Route::get('/staff/schedule', [StaffController::class, 'schedule'])
    ->middleware('can:lihat-jadwal')
    ->name('staff.schedule');