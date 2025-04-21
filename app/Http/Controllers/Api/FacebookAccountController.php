<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PcProfile;
use App\Models\FacebookAccount;
use App\Models\GmailAccount;
use App\Models\ChromeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacebookAccountController extends Controller
{
    /**
     * Get one pending Facebook account and update its status to processing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingAccounts(Request $request)
    {
        // Get access token from Authorization header
        $accessToken = $request->header('Authorization');
        
        // Remove 'Bearer ' prefix if present
        if (str_starts_with($accessToken, 'Bearer ')) {
            $accessToken = substr($accessToken, 7);
        }

        // Check if access token is provided
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token is required in the Authorization header',
                'data' => [
                    'error_code' => 'MISSING_ACCESS_TOKEN',
                    'suggestion' => 'Please provide the access token in the Authorization header'
                ]
            ], 401);
        }

        // Find PC profile by access token
        $pcProfile = PcProfile::where('access_token', $accessToken)->first();

        // Check if PC profile exists
        if (!$pcProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found. Please generate a new access token or check your credentials.',
                'data' => [
                    'error_code' => 'INVALID_ACCESS_TOKEN',
                    'suggestion' => 'Please use the /api/generate-token endpoint to get a new access token'
                ]
            ], 401);
        }

        // Check if PC profile is active
        if ($pcProfile->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'PC profile is not active',
                'data' => [
                    'error_code' => 'INACTIVE_PC_PROFILE',
                    'suggestion' => 'Please activate your PC profile first'
                ]
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Get one pending Facebook account for this PC profile with related data
            $pendingAccount = FacebookAccount::with(['gmailAccount', 'chromeProfile'])
                ->where('pc_profile_id', $pcProfile->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$pendingAccount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No pending Facebook accounts available',
                    'data' => [
                        'error_code' => 'NO_PENDING_ACCOUNTS',
                        'suggestion' => 'Please try again later or contact support'
                    ]
                ], 404);
            }

            // Create new Chrome profile if none exists
            if (!$pendingAccount->chromeProfile) {
                // Get the user agent range from PC profile
                $userAgentRange = $pcProfile->user_agent;
                if ($userAgentRange) {
                    // Split the range into min and max
                    list($min, $max) = explode('-', $userAgentRange);
                    // Generate random version between min and max
                    $chromeVersion = rand((int)$min, (int)$max);
                    
                    // Define build numbers and patch ranges for each Chrome version
                    $chromeVersions = [
                        114 => ['build' => 5735, 'min_patch' => 50, 'max_patch' => 180],
                        115 => ['build' => 5790, 'min_patch' => 80, 'max_patch' => 170],
                        116 => ['build' => 5845, 'min_patch' => 70, 'max_patch' => 160],
                        117 => ['build' => 5911, 'min_patch' => 50, 'max_patch' => 150],
                        118 => ['build' => 5993, 'min_patch' => 60, 'max_patch' => 130],
                        119 => ['build' => 6045, 'min_patch' => 60, 'max_patch' => 150],
                        120 => ['build' => 6099, 'min_patch' => 50, 'max_patch' => 140],
                        121 => ['build' => 6172, 'min_patch' => 70, 'max_patch' => 160],
                        122 => ['build' => 6233, 'min_patch' => 80, 'max_patch' => 170],
                        123 => ['build' => 6300, 'min_patch' => 90, 'max_patch' => 180]
                    ];

                    // If version is not in our list, find the closest valid version
                    if (!isset($chromeVersions[$chromeVersion])) {
                        $validVersions = array_keys($chromeVersions);
                        $closestVersion = null;
                        $minDiff = PHP_INT_MAX;

                        foreach ($validVersions as $validVersion) {
                            $diff = abs($chromeVersion - $validVersion);
                            if ($diff < $minDiff) {
                                $minDiff = $diff;
                                $closestVersion = $validVersion;
                            }
                        }
                        $chromeVersion = $closestVersion;
                    }

                    // Get build number and patch range for the selected Chrome version
                    $versionInfo = $chromeVersions[$chromeVersion];
                    $buildNumber = $versionInfo['build'];
                    $patchNumber = rand($versionInfo['min_patch'], $versionInfo['max_patch']);

                    $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$chromeVersion}.0.{$buildNumber}.{$patchNumber} Safari/537.36";
                } else {
                    // Set to null if no range is set
                    $userAgent = null;
                }

                // Get the root directory from PC profile
                $rootDirectory = $pcProfile->profile_root_directory;

                $chromeProfile = ChromeProfile::create([
                    'pc_profile_id' => $pcProfile->id,
                    'profile_directory' => $rootDirectory . '\\chrome_' . uniqid(),
                    'user_agent' => $userAgent,
                    'status' => 'pending'
                ]);

                // Update Facebook account with new Chrome profile
                $pendingAccount->chrome_profile_id = $chromeProfile->id;
            }

            // Update the account status to processing
            $pendingAccount->status = 'processing';
            $pendingAccount->save();

            // If there's a Gmail account, update its Chrome profile too
            if ($pendingAccount->gmailAccount && !$pendingAccount->gmailAccount->chromeProfile) {
                $pendingAccount->gmailAccount->chrome_profile_id = $pendingAccount->chrome_profile_id;
                $pendingAccount->gmailAccount->save();
            }

            DB::commit();

            // Refresh the account with latest relations
            $pendingAccount->refresh();

            // Prepare response data
            $responseData = [
                'facebook_account' => [
                    'id' => $pendingAccount->id,
                    'email' => $pendingAccount->email,
                    'password' => $pendingAccount->password,
                    'status' => $pendingAccount->status,
                    'pc_profile_id' => $pendingAccount->pc_profile_id,
                    'chrome_profile_id' => $pendingAccount->chrome_profile_id,
                    'have_page' => $pendingAccount->have_page,
                    'account_cookies' => $pendingAccount->account_cookies,
                    'gmail_account_id' => $pendingAccount->gmail_account_id,
                    'created_at' => $pendingAccount->created_at,
                    'note' => $pendingAccount->note
                ]
            ];

            // If there's a related Gmail account, include its information
            if ($pendingAccount->gmailAccount) {
                $responseData['gmail_account'] = [
                    'id' => $pendingAccount->gmailAccount->id,
                    'email' => $pendingAccount->gmailAccount->email,
                    'password' => $pendingAccount->gmailAccount->password,
                    'status' => $pendingAccount->gmailAccount->status,
                    'created_at' => $pendingAccount->gmailAccount->created_at,
                    'note' => $pendingAccount->gmailAccount->note
                ];
            }

            // Include Chrome profile information
            if ($pendingAccount->chromeProfile) {
                $responseData['chrome_profile'] = [
                    'id' => $pendingAccount->chromeProfile->id,
                    'profile_directory' => $pendingAccount->chromeProfile->profile_directory,
                    'user_agent' => $pendingAccount->chromeProfile->user_agent,
                    'status' => $pendingAccount->chromeProfile->status,
                    'created_at' => $pendingAccount->chromeProfile->created_at
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Pending Facebook account retrieved and updated to processing',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process Facebook account',
                'data' => [
                    'error_code' => 'PROCESSING_ERROR',
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
} 