<?php

use App\Http\Controllers\PublicConsignmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Formulir Publik Penitipan Barang
Route::get('/titip', [PublicConsignmentController::class, 'index'])->name('titip.index');
Route::post('/titip', [PublicConsignmentController::class, 'store'])->name('titip.store');
