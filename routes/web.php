<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\ShareViewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Share Link Public Route
Route::get('/share/{token}', [ShareViewController::class, 'show'])->name('share.show');

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

    // ── Dokumen ─────────────────────────────────────────
    Route::get('/dokumen', [DocumentController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/create', [DocumentController::class, 'create'])->name('dokumen.create');
    Route::post('/dokumen', [DocumentController::class, 'store'])->name('dokumen.store');
    Route::get('/dokumen/{dokumen}', [DocumentController::class, 'show'])->name('dokumen.show');
    Route::get('/dokumen/{dokumen}/edit', [DocumentController::class, 'edit'])->name('dokumen.edit');
    Route::put('/dokumen/{dokumen}', [DocumentController::class, 'update'])->name('dokumen.update');
    Route::delete('/dokumen/{dokumen}', [DocumentController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/{dokumen}/download', [DocumentController::class, 'download'])->name('dokumen.download');
    Route::get('/dokumen/{dokumen}/preview', [DocumentController::class, 'preview'])->name('dokumen.preview');

    // ── Share Link ──────────────────────────────────────
    Route::post('/dokumen/{dokumen}/share', [ShareLinkController::class, 'store'])->name('dokumen.share');
    Route::delete('/share/{link}/revoke', [ShareLinkController::class, 'destroy'])->name('share.revoke');

    // ── Recycle Bin ──────────────────────────────────────
    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::post('/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore'])->name('recycle-bin.restore');

    // ── Activity Log ─────────────────────────────────────
    Route::get('/aktivitas', [ActivityLogController::class, 'index'])->name('aktivitas.index');

    // Placeholder routes for future features
    // Route::get('/laporan', ...)
    // Route::get('/aktivitas', ...)
    // Route::get('/profil', ...)
});

// Admin-only routes
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    // Recycle Bin (Admin only actions)
    Route::delete('/recycle-bin/empty', [RecycleBinController::class, 'empty'])->name('recycle-bin.empty');
    Route::delete('/recycle-bin/{id}', [RecycleBinController::class, 'destroy'])->name('recycle-bin.destroy');

    // Placeholder routes for admin features
    // Route::resource('/users', UserController::class)
    // Route::resource('/kategori', CategoryController::class)
});
