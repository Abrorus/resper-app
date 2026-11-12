<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;

// STAFF MANAGEMENT
Route::get('/staff', [StaffController::class, 'index'])
    ->name('staff.index');

Route::post('/staff', [StaffController::class, 'store'])
    ->name('staff.store');

Route::put('/staff/{staff}', [StaffController::class, 'update'])
    ->name('staff.update');

Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])
    ->name('staff.destroy');

// STAFF SCHEDULE
Route::get('/staff/schedule', [StaffController::class, 'schedule'])
    ->name('staff.schedule');

Route::post('/staff/schedule', [StaffController::class, 'storeSchedule'])
    ->name('staff.schedule.store');

Route::put('/staff/schedule/{staffSchedule}', [StaffController::class, 'updateSchedule'])
    ->name('staff.schedule.update');

Route::delete('/staff/schedule/{staffSchedule}', [StaffController::class, 'destroySchedule'])
    ->name('staff.schedule.destroy');