<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use App\Models\PcProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateFacebookAccountController extends Controller
{
    /**
     * Update Facebook account information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // Get access token from header
            $accessToken = $request->header('Authorization');
            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access token is required'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $accessToken = str_replace('Bearer ', '', $accessToken);

            // Find PC profile by access token
            $pcProfile = PcProfile::where('access_token', $accessToken)->first();
            if (!$pcProfile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid access token'
                ], 401);
            }

            // Check if PC profile is active
            if ($pcProfile->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PC profile is not active'
                ], 403);
            }

            // Validate incoming data including ID
            $validatedData = $request->validate([
                'id' => 'required|integer|exists:facebook_accounts,id',
                'note' => 'nullable|string',
                'lang' => ['nullable', 'string', 'max:10', Rule::in(['en', 'bn', 'as', 'ar', 'fr', 'es', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh'])],
                'account_cookies' => 'nullable|array',
                'status' => 'required|string|in:active,inactive,remove',
                'have_use' => 'nullable|boolean',
                'have_page' => 'nullable|boolean',
                'have_post' => 'nullable|boolean',
                'chrome_profile' => 'nullable|array',
                'chrome_profile.user_agent' => 'nullable|string',
                'chrome_profile.status' => 'nullable|string|in:pending,active,inactive,blocked'
            ]);

            // Find the Facebook account
            $facebookAccount = FacebookAccount::findOrFail($validatedData['id']);

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Update Facebook account
                $facebookAccount->update([
                    'note' => $validatedData['note'] ?? $facebookAccount->note,
                    'lang' => $validatedData['lang'] ?? $facebookAccount->lang,
                    'account_cookies' => $validatedData['account_cookies'] ?? $facebookAccount->account_cookies,
                    'status' => $validatedData['status'],
                    'have_use' => $validatedData['have_use'] ?? $facebookAccount->have_use,
                    'have_page' => $validatedData['have_page'] ?? $facebookAccount->have_page,
                    'have_post' => $validatedData['have_post'] ?? $facebookAccount->have_post
                ]);

                // Update Chrome profile if provided
                if (isset($validatedData['chrome_profile']) && $facebookAccount->chromeProfile) {
                    $facebookAccount->chromeProfile->update([
                        'user_agent' => $validatedData['chrome_profile']['user_agent'] ?? $facebookAccount->chromeProfile->user_agent,
                        'status' => $validatedData['chrome_profile']['status'] ?? $facebookAccount->chromeProfile->status
                    ]);
                }

                // Commit the transaction
                DB::commit();

                // Return success response with updated data
                return response()->json([
                    'success' => true,
                    'message' => 'Facebook account updated successfully',
                    'data' => [
                        'facebook_account' => [
                            'id' => $facebookAccount->id,
                            'email' => $facebookAccount->email,
                            'status' => $facebookAccount->status,
                            'note' => $facebookAccount->note,
                            'lang' => $facebookAccount->lang,
                            'have_use' => $facebookAccount->have_use,
                            'have_page' => $facebookAccount->have_page,
                            'have_post' => $facebookAccount->have_post,
                            'account_cookies' => $facebookAccount->account_cookies
                        ],
                        'chrome_profile' => $facebookAccount->chromeProfile ? [
                            'id' => $facebookAccount->chromeProfile->id,
                            'user_agent' => $facebookAccount->chromeProfile->user_agent,
                            'status' => $facebookAccount->chromeProfile->status,
                            'profile_directory' => $facebookAccount->chromeProfile->profile_directory
                        ] : null
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                Log::error('Error in update: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
} 