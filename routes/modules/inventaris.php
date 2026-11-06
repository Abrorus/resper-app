<?php

use Illuminate\Support\Facades\Route;

Route::view('/inventaris', 'placeholder', ['modul' => 'Inventaris'])->name('inventory.index');