<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\ChromeProfileController;
use App\Http\Controllers\Backend\FacebookAccountController;
use App\Http\Controllers\Backend\FacebookQuickCheckController;
use App\Http\Controllers\Backend\GmailAccountController;
use App\Http\Controllers\Backend\PcProfileController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\SubmissionBatchController;

// Admin Authentication Routes
Route::prefix('secure-admin')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// Protected Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('/users/{user}/add-funds', [UserController::class, 'showAddFunds'])->name('users.add-funds-form');
    Route::post('/users/{user}/add-funds', [UserController::class, 'addFunds'])->name('users.add-funds');
    Route::resource('users', UserController::class);
    
    // Order Management Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::post('/orders/bulk-update', [OrderController::class, 'bulkUpdate'])->name('orders.bulk-update');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/orders/{order}/uid', [OrderController::class, 'updateUid'])->name('orders.update-uid');
    Route::post('/orders/{order}/uid', [OrderController::class, 'updateUid']);
    Route::patch('/orders/{order}/link', [OrderController::class, 'updateLink'])->name('orders.update-link');
    Route::post('/orders/{order}/link', [OrderController::class, 'updateLink']);
    Route::patch('/orders/{order}/update-start-count', [OrderController::class, 'updateStartCount'])->name('orders.update-start-count');
    Route::post('/orders/{order}/update-start-count', [OrderController::class, 'updateStartCount']);
    
    // Service Management Routes
    Route::resource('services', ServiceController::class);
    
    // Payment Management Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/pending', [PaymentController::class, 'pending'])->name('payments.pending');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::put('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::put('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    
    // Chrome Profile Routes
    Route::resource('chrome', ChromeProfileController::class)->except(['create', 'store']);
    Route::get('chrome-profiles/by-pc/{pcProfile}', [ChromeProfileController::class, 'getByPcProfile'])
        ->name('chrome-profiles.by-pc');
    
    // Facebook Account Routes
    Route::resource('facebook', FacebookAccountController::class);
    Route::post('facebook/{id}/restore', [FacebookAccountController::class, 'restore'])->name('facebook.restore');
    Route::delete('facebook/{id}/force-delete', [FacebookAccountController::class, 'forceDelete'])->name('facebook.force-delete');
    Route::match(['post', 'delete'], 'facebook-bulk-update', [FacebookAccountController::class, 'bulkUpdate'])->name('facebook-bulk-update');

    // Facebook Quick Check Routes
    Route::resource('facebook-quick-check', FacebookQuickCheckController::class);
    Route::post('facebook-quick-check/{id}/restore', [FacebookQuickCheckController::class, 'restore'])
        ->name('facebook-quick-check.restore');
    Route::delete('facebook-quick-check/{id}/force-delete', [FacebookQuickCheckController::class, 'forceDelete'])
        ->name('facebook-quick-check.force-delete');
    Route::post('facebook-quick-check/{id}/toggle-valid', [FacebookQuickCheckController::class, 'toggleValid'])
        ->name('facebook-quick-check.toggle-valid');
    Route::post('facebook-quick-check/{id}/quick-check', [FacebookQuickCheckController::class, 'quickCheck'])
        ->name('facebook-quick-check.quick-check');
    Route::post('facebook-quick-check/{id}/transfer', [FacebookQuickCheckController::class, 'transferToFacebookAccount'])
        ->name('facebook-quick-check.transfer');
    Route::post('facebook-quick-check/transfer-all-active', [FacebookQuickCheckController::class, 'transferAllActive'])
        ->name('facebook-quick-check.transfer-all-active');
    Route::match(['post', 'delete'], 'facebook-quick-check-bulk-update', [FacebookQuickCheckController::class, 'bulkUpdate'])
        ->name('facebook-quick-check-bulk-update');

    // Gmail Account Routes
    Route::resource('gmail', GmailAccountController::class);
    Route::post('gmail/{id}/restore', [GmailAccountController::class, 'restore'])->name('gmail.restore');
    Route::delete('gmail/{id}/force-delete', [GmailAccountController::class, 'forceDelete'])->name('gmail.force-delete');

    // PC Profile Routes
    Route::resource('pc-profiles', PcProfileController::class);

    // Settings Routes
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::put('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.update-security');
    Route::get('/settings/system', [SettingsController::class, 'systemSettings'])->name('settings.system');
    Route::post('/settings/system', [SettingsController::class, 'updateSystemSettings'])->name('settings.system.update');

    // Submission Batch Routes
    Route::resource('submission-batch', SubmissionBatchController::class);
    Route::post('submission-batch/{id}/restore', [SubmissionBatchController::class, 'restore'])->name('submission-batch.restore');
    Route::delete('submission-batch/{id}/force-delete', [SubmissionBatchController::class, 'forceDelete'])->name('submission-batch.force-delete');
    Route::get('submission-batch/{submissionBatch}/report', [SubmissionBatchController::class, 'generateReport'])->name('submission-batch.report');
}); 