<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Root → scanner (public entry point)
Route::get('/', fn() => redirect()->route('scanner'));

// ─── Public QR routes (no auth required) ────────────────────────────────────
Route::get('/scan', [ScanController::class, 'scanner'])->name('scanner');
Route::get('/scan/{uuid}', [ScanController::class, 'scan'])->name('scan');
Route::get('/profile/{uuid}', [ScanController::class, 'profile'])->name('profile.show');

// ─── Admin routes (auth required) ───────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->parameters(['users' => 'user_profile']);
});

// Breeze default dashboard redirect
Route::get('/dashboard', fn() => redirect()->route('admin.users.index'))
    ->middleware(['auth'])
    ->name('dashboard');

require __DIR__ . '/auth.php';
