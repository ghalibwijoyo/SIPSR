<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect('/dashboard');
});

// Guest routes (unauthenticated users only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout (authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder routes for future features
    // Route::get('/dokumen', ...)
    // Route::get('/recycle-bin', ...)
    // Route::get('/laporan', ...)
    // Route::get('/aktivitas', ...)
    // Route::get('/profil', ...)
});

// Admin-only routes
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    // Placeholder routes for admin features
    // Route::resource('/users', UserController::class)
    // Route::resource('/kategori', CategoryController::class)
});
