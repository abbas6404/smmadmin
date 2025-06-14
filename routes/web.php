<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\ForgotPasswordController;
use App\Http\Controllers\Frontend\ResetPasswordController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\FundsController;
use App\Http\Controllers\Frontend\DownloadController;
use App\Http\Controllers\Frontend\UidFinderController;

// Public Download Page
Route::get('/download', [DownloadController::class, 'index'])->name('downloads');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('frontend.dashboard');
    })->name('dashboard');
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/{service}', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/order/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/order/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/order/{order}/uid', [OrderController::class, 'updateUid'])->name('orders.update-uid');
    
    // Mass order routes
    Route::get('/orders/mass-create/{service}', [OrderController::class, 'massCreate'])->name('orders.mass-create');
    Route::post('/orders/mass-store/{service}', [OrderController::class, 'massStore'])->name('orders.mass-store');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/security', [ProfileController::class, 'security'])->name('profile.security');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Funds Management
    Route::get('/funds', [FundsController::class, 'index'])->name('funds.index');
    Route::get('/funds/add', [FundsController::class, 'add'])->name('funds.add');
    Route::post('/funds/process', [FundsController::class, 'process'])->name('funds.process');
    
    // UID Finder Tool
    Route::get('/tools/uid-finder', [UidFinderController::class, 'index'])->name('uid-finder');
    Route::post('/tools/uid-finder/extract', [UidFinderController::class, 'extract'])->name('uid-finder.extract');
});

Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    // API Documentation
    Route::get('/api/docs', function () {
        return view('backend.api.docs');
    })->name('api.docs');

    // Facebook Status Routes
    Route::get('/facebook/status', [\App\Http\Controllers\Backend\FacebookStatusController::class, 'getStatus'])
        ->name('facebook.status');
    Route::post('/facebook/reset-counts', [\App\Http\Controllers\Backend\FacebookStatusController::class, 'resetCounts'])
        ->name('facebook.reset-counts');

    // Submission Batch Routes
    Route::get('submission-batch/{submissionBatch}/update-count', [\App\Http\Controllers\Backend\SubmissionBatchController::class, 'updateCount'])
        ->name('submission-batch.update-count');

    // Disk Management Routes
    Route::resource('disks', \App\Http\Controllers\Backend\DiskController::class);
});
