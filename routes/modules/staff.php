<?php

use App\Http\Controllers\Staff\StaffController;
use Illuminate\Support\Facades\Route;

// Data staff, khusus manajer
Route::middleware('can:kelola-staf')->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
});

// Jadwal, semua role boleh melihat
Route::get('/staff/schedule', [StaffController::class, 'schedule'])
    ->middleware('can:lihat-jadwal')
    ->name('staff.schedule');

// Jadwal, tambah/ubah/hapus khusus manajer
Route::middleware('can:kelola-jadwal')->group(function () {
    Route::post('/staff/schedule', [StaffController::class, 'storeSchedule'])->name('staff.schedule.store');
    Route::put('/staff/schedule/{staffSchedule}', [StaffController::class, 'updateSchedule'])->name('staff.schedule.update');
    Route::delete('/staff/schedule/{staffSchedule}', [StaffController::class, 'destroySchedule'])->name('staff.schedule.destroy');
});