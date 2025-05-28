<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PcAuthController;
use App\Http\Controllers\Api\FacebookAccountController;
use App\Http\Controllers\Api\UpdateFacebookAccountController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\FacebookQuickCheckController;
use App\Http\Controllers\Api\FacebookUidController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PC Authentication Routes
Route::post('/generate-token', [PcAuthController::class, 'generateToken']);
Route::post('/get-profile', [PcAuthController::class, 'getProfile']);

// Facebook Account Routes
Route::get('/pending-facebook-accounts', [FacebookAccountController::class, 'getPendingAccounts']);
Route::post('/update-facebook-account', [UpdateFacebookAccountController::class, 'update']);

// Order Routes
Route::get('/orders', [OrderController::class, 'getOrders']);

// Facebook UID Extraction
Route::post('/extract-facebook-uid', [FacebookUidController::class, 'extractUid']);

/*
|--------------------------------------------------------------------------
| Facebook Quick Check API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('facebook-quick-check')->group(function () {
    Route::get('/get-account', [FacebookQuickCheckController::class, 'getAccountForChecking']);
    Route::post('/update-check/{id}', [FacebookQuickCheckController::class, 'updateCheckResult']);
}); 