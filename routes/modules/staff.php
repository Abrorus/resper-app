
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;

Route::get('/staff', [StaffController::class, 'index'])
    ->name('staff.index');

Route::get('/staff/schedule', [StaffController::class, 'schedule'])
    ->name('staff.schedule');
