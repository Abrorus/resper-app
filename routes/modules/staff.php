<?php

use Illuminate\Support\Facades\Route;

Route::view('/staff', 'placeholder', ['modul' => 'Staff', 'judul' => 'Data Staff'])->name('staff.index');
Route::view('/staff/schedule', 'placeholder', ['modul' => 'Staff', 'judul' => 'Jadwal Staff'])->name('staff.schedule');