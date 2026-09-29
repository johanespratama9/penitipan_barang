<?php

use App\Http\Controllers\PublicConsignmentController;
use App\Livewire\PosTerminal;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PenitipAuthController;

Route::get('/', function () {
    return view('welcome');
});

// ── Pendaftaran & Autentikasi Khusus Mitra Penitip ─────────────
Route::get('/daftar-penitip', [PenitipAuthController::class, 'showRegisterForm'])->name('penitip.register');
Route::post('/daftar-penitip', [PenitipAuthController::class, 'register'])->name('penitip.register.store');
Route::post('/penitip/logout', [PenitipAuthController::class, 'logout'])->name('penitip.logout');

// ── Formulir Penitipan Barang (Wajib Login Mitra) ───────────────
Route::get('/titip', [PublicConsignmentController::class, 'index'])->name('titip.index');
Route::post('/titip', [PublicConsignmentController::class, 'store'])->name('titip.store');

// ── POS Terminal: standalone full-screen, wajib login ────────────
// Unauthenticated users are sent to Filament login page
Route::get('/login', fn() => redirect('/dasbor/login'))->name('login');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/pos', PosTerminal::class)->name('pos.terminal');
});
