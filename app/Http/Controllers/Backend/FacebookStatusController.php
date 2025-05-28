<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FacebookStatusController extends Controller
{
    /**
     * Get the current Facebook account status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus(Request $request)
    {
        try {
            // Get the last reset time
            $lastRun = Setting::where('key', 'facebook_reset_last_run')->first();
            $lastRunTime = $lastRun ? $lastRun->value : null;
            
            // Get the daily limit
            $limit = Setting::where('key', 'facebook_account_daily_use_limit')->first();
            $dailyLimit = $limit ? (int)$limit->value : 0;
            
            // Get account statistics
            $totalAccounts = FacebookAccount::count();
            $usedAccounts = FacebookAccount::where('use_count', '>', 0)->count();
            $maxedOutAccounts = FacebookAccount::where('use_count', '>=', $dailyLimit)->count();
            $availableAccounts = $totalAccounts - $maxedOutAccounts;
            
            // Calculate average use count for accounts with uses
            $avgUseCount = $usedAccounts > 0 
                ? FacebookAccount::where('use_count', '>', 0)->avg('use_count') 
                : 0;
            
            // Get usage distribution
            $usageStats = FacebookAccount::selectRaw('use_count, COUNT(*) as count')
                ->groupBy('use_count')
                ->orderBy('use_count')
                ->get();
            
            // Format data for response
            $distribution = $usageStats->map(function($item) use ($dailyLimit) {
                return [
                    'use_count' => $item->use_count,
                    'count' => $item->count,
                    'status' => $item->use_count >= $dailyLimit ? 'at_limit' : 'available'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'last_reset' => [
                        'timestamp' => $lastRunTime,
                        'formatted' => $lastRunTime ? Carbon::parse($lastRunTime)->format('Y-m-d H:i:s') : 'Never',
                        'time_ago' => $lastRunTime ? Carbon::parse($lastRunTime)->diffForHumans() : null
                    ],
                    'daily_limit' => $dailyLimit,
                    'accounts' => [
                        'total' => $totalAccounts,
                        'used' => $usedAccounts,
                        'used_percent' => $totalAccounts > 0 ? round(($usedAccounts / $totalAccounts) * 100) : 0,
                        'at_limit' => $maxedOutAccounts,
                        'at_limit_percent' => $totalAccounts > 0 ? round(($maxedOutAccounts / $totalAccounts) * 100) : 0,
                        'available' => $availableAccounts,
                        'available_percent' => $totalAccounts > 0 ? round(($availableAccounts / $totalAccounts) * 100) : 0,
                        'avg_use_count' => round($avgUseCount, 1)
                    ],
                    'distribution' => $distribution
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Facebook status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Manually trigger the reset of Facebook account use counts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetCounts(Request $request)
    {
        try {
            // We'll skip permission check for now - only admin routes should access this anyway
            // Get the count of accounts that will be reset
            $count = FacebookAccount::where('use_count', '>', 0)->count();
            
            // Reset the use counts
            FacebookAccount::query()->update(['use_count' => 0]);
            
            // Update the last run timestamp
            $now = Carbon::now()->toDateTimeString();
            Setting::updateOrCreate(
                ['key' => 'facebook_reset_last_run'],
                ['value' => $now]
            );
            
            return response()->json([
                'success' => true,
                'message' => "Successfully reset use counts for {$count} Facebook accounts.",
                'reset_time' => $now
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting Facebook account use counts: ' . $e->getMessage()
            ], 500);
        }
    }
} 