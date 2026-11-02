
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;

// Halaman utama Laravel
Route::get('/', function () {
    return view('welcome');
});

// Halaman Staff Management
Route::get('/staff', [StaffController::class, 'index'])
    ->name('staff.index');
// Staff Schedule
Route::get('/staff/schedule', [StaffController::class, 'schedule'])
    ->name('staff.schedule');