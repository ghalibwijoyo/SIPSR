<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\ShareViewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Share Link Public Routes (no auth required)
Route::controller(ShareViewController::class)->prefix('share')->name('share.')->group(function () {
    Route::get('/{token}', 'show')->name('show');
    Route::get('/{token}/download', 'download')->name('download');
    Route::get('/{token}/preview', 'preview')->name('preview');
});

// Redirect root to dashboard or login
Route::redirect('/', '/dashboard');

// Guest routes (unauthenticated users only)
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
});

// Logout (authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Dokumen ─────────────────────────────────────────
    Route::controller(DocumentController::class)->prefix('dokumen')->name('dokumen.')->group(function () {
        Route::post('/bulk-download', 'bulkDownload')->name('bulk-download');
        Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::get('/{dokumen}/download', 'download')->name('download');
        Route::get('/{dokumen}/preview', 'preview')->name('preview');
    });
    Route::resource('dokumen', DocumentController::class)->parameters([
        'dokumen' => 'dokumen',
    ]);

    // ── Share Link ──────────────────────────────────────
    Route::post('/dokumen/{dokumen}/share', [ShareLinkController::class, 'store'])->name('dokumen.share');
    Route::delete('/share/{link}/revoke', [ShareLinkController::class, 'destroy'])->name('share.revoke');

    // ── Recycle Bin ──────────────────────────────────────
    Route::controller(RecycleBinController::class)->prefix('recycle-bin')->name('recycle-bin.')->group(function () {
        Route::post('/bulk-restore', 'bulkRestore')->name('bulk-restore');
        Route::post('/{id}/restore', 'restore')->name('restore');
        Route::get('/', 'index')->name('index');
    });

    // ── Activity Log ─────────────────────────────────────
    Route::get('/aktivitas', [ActivityLogController::class, 'index'])->name('aktivitas.index');

    // ── Laporan ──────────────────────────────────────────
    Route::controller(LaporanController::class)->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/export-excel', 'exportExcel')->name('export.excel');
        Route::get('/export-pdf', 'exportPdf')->name('export.pdf');
        Route::get('/print-pdf', 'printPdf')->name('print.pdf');
        Route::get('/', 'index')->name('index');
    });

    // ── Profil ───────────────────────────────────────────
    Route::controller(ProfilController::class)->prefix('profil')->name('profil.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::put('/nama', 'updateNama')->name('update-nama');
        Route::put('/password', 'updatePassword')->name('update-password');
    });
});

// Admin-only routes
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    // Recycle Bin (Admin only actions)
    Route::controller(RecycleBinController::class)->prefix('recycle-bin')->name('recycle-bin.')->group(function () {
        Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::delete('/empty', 'empty')->name('empty');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // User Management
    Route::controller(UserController::class)->prefix('admin/users')->name('users.')->group(function () {
        Route::patch('/{user}/toggle-active', 'toggleActive')->name('toggle-active');
        Route::patch('/{user}/reset-password', 'resetPassword')->name('reset-password');
    });
    Route::resource('admin/users', UserController::class)->names('users')->except(['create', 'show', 'edit']);

    // Category Management
    Route::resource('admin/categories', CategoryController::class)->names('categories')->except(['create', 'show', 'edit']);

    // Bank Management
    Route::resource('admin/banks', BankController::class)->names('banks')->except(['create', 'show', 'edit']);
});
