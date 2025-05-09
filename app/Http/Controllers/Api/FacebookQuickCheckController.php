<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FacebookQuickCheck;
use Carbon\Carbon;

class FacebookQuickCheckController extends Controller
{
    /**
     * Get a single pending account for checking
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccountForChecking(Request $request)
    {
        // Validate API key from request header
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized. Invalid API key.'], 401);
        }

        // Get one pending account
        $account = FacebookQuickCheck::where('status', 'pending')
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();

        if (!$account) {
            return response()->json(['message' => 'No accounts available for checking.'], 404);
        }

        // Mark the account as processing
        $account->status = 'processing';
        $account->save();

        return response()->json([
            'id' => $account->id,
            'email' => $account->email,
            'password' => $account->password,
            'two_factor_secret' => $account->two_factor_secret,
        ]);
    }

    /**
     * Update the check result for an account
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCheckResult(Request $request, $id)
    {
        // Validate API key from request header
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized. Invalid API key.'], 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,blocked',
            'check_result' => 'nullable|string',
            'account_cookies' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the account
        $account = FacebookQuickCheck::find($id);
        
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        // Update account
        $account->status = $request->status;
        $account->check_result = $request->check_result;
        $account->checked_by = 'Chrome Extension';
        $account->last_checked_at = now();
        $account->check_count = $account->check_count + 1;

        if ($request->has('account_cookies')) {
            $account->account_cookies = $request->account_cookies;
        }
        
        if ($request->has('notes')) {
            $account->notes = $request->notes;
        }

        $account->save();

        return response()->json(['message' => 'Account updated successfully']);
    }

    /**
     * Validate the API key from the request header
     *
     * @param Request $request
     * @return bool
     */
    private function validateApiKey(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
        $validApiKey = config('services.facebook_quick_check.api_key');
        
        return $apiKey && $apiKey === $validApiKey;
    }
} 