<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComplaintManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicComplaintController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Akses Bebas & Siswa)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicComplaintController::class, 'index'])->name('home');
Route::get('/pengaduan/buat', [PublicComplaintController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [PublicComplaintController::class, 'store'])->middleware('throttle:15,1')->name('pengaduan.store');
Route::get('/pengaduan/lacak', [PublicComplaintController::class, 'track'])->name('pengaduan.track');
Route::get('/pengaduan/{ticket_code}', [PublicComplaintController::class, 'show'])->name('pengaduan.show');
Route::post('/pengaduan/{ticket_code}/rate', [PublicComplaintController::class, 'rate'])->name('pengaduan.rate');
Route::post('/pengaduan/{ticket_code}/response', [PublicComplaintController::class, 'addResponse'])->name('pengaduan.response');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Complaint Management Workflow
    Route::get('/pengaduan', [ComplaintManagementController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{ticket_code}', [ComplaintManagementController::class, 'show'])->name('pengaduan.show');
    
    // Guru Piket & Admin Action
    Route::post('/pengaduan/{ticket_code}/verify', [ComplaintManagementController::class, 'verifyAndAssign'])->name('pengaduan.verify');
    Route::post('/pengaduan/{ticket_code}/reject', [ComplaintManagementController::class, 'reject'])->name('pengaduan.reject');
    
    // Petugas Action
    Route::post('/pengaduan/{ticket_code}/process', [ComplaintManagementController::class, 'startProcess'])->name('pengaduan.process');
    Route::post('/pengaduan/{ticket_code}/resolve', [ComplaintManagementController::class, 'submitResolution'])->name('pengaduan.resolve');
    
    // Kepala Sekolah Action
    Route::post('/pengaduan/{ticket_code}/approve', [ComplaintManagementController::class, 'approveResolution'])->name('pengaduan.approve');
    Route::post('/pengaduan/{ticket_code}/revision', [ComplaintManagementController::class, 'requestRevision'])->name('pengaduan.revision');

    // Discussion / Notes
    Route::post('/pengaduan/{ticket_code}/response', [ComplaintManagementController::class, 'addResponse'])->name('pengaduan.response');

    // Categories Management (Admin / Staff)
    Route::resource('kategori', CategoryController::class)->except(['create', 'edit', 'show']);

    // Reports & Analytics (Kepsek & Admin)
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/print', [ReportController::class, 'print'])->name('laporan.print');
});
