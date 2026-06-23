<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\ShareViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Share Link Public Routes (no auth required)
Route::get('/share/{token}', [ShareViewController::class, 'show'])->name('share.show');
Route::get('/share/{token}/download', [ShareViewController::class, 'download'])->name('share.download');
Route::get('/share/{token}/preview', [ShareViewController::class, 'preview'])->name('share.preview');

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
    Route::post('/dokumen/bulk-download', [DocumentController::class, 'bulkDownload'])->name('dokumen.bulk-download');
    Route::delete('/dokumen/bulk-delete', [DocumentController::class, 'bulkDestroy'])->name('dokumen.bulk-delete');
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
    Route::post('/recycle-bin/bulk-restore', [RecycleBinController::class, 'bulkRestore'])->name('recycle-bin.bulk-restore');
    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::post('/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore'])->name('recycle-bin.restore');

    // ── Activity Log ─────────────────────────────────────
    Route::get('/aktivitas', [ActivityLogController::class, 'index'])->name('aktivitas.index');

    // ── Laporan ──────────────────────────────────────────
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/print-pdf', [LaporanController::class, 'printPdf'])->name('laporan.print.pdf');

    // ── Profil ───────────────────────────────────────────
    Route::get('/profil', [\App\Http\Controllers\ProfilController::class, 'show'])->name('profil.show');
    Route::put('/profil/nama', [\App\Http\Controllers\ProfilController::class, 'updateNama'])->name('profil.update-nama');
    Route::put('/profil/password', [\App\Http\Controllers\ProfilController::class, 'updatePassword'])->name('profil.update-password');
});

// Admin-only routes
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    // Recycle Bin (Admin only actions)
    Route::delete('/recycle-bin/bulk-delete', [RecycleBinController::class, 'bulkDestroy'])->name('recycle-bin.bulk-delete');
    Route::delete('/recycle-bin/empty', [RecycleBinController::class, 'empty'])->name('recycle-bin.empty');
    Route::delete('/recycle-bin/{id}', [RecycleBinController::class, 'destroy'])->name('recycle-bin.destroy');

    // User Management
    Route::resource('/admin/users', UserController::class)->names('users')->except(['create', 'show', 'edit']);
    Route::patch('/admin/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::patch('/admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Category Management
    Route::resource('/admin/categories', CategoryController::class)->names('categories')->except(['create', 'show', 'edit']);
});
