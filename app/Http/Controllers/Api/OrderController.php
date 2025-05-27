<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PcProfile;
use App\Models\FacebookAccount;
use App\Models\GmailAccount;
use App\Models\Service;
use App\Models\ChromeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get pending and processing orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders(Request $request)
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
            
            // Check for auto shutdown first - return early if enabled
            if ($pcProfile->auto_shutdown) {
                return response()->json([
                    'status' => 'shutdown',
                    'message' => 'Auto shutdown activated'
                ]);
            }

            // First, mark orders with empty link_uid as failed
            Order::whereIn('status', ['pending', 'processing'])
                ->where(function ($query) {
                    $query->whereNull('link_uid')
                        ->orWhere('link_uid', '');
                })
                ->update([
                    'status' => 'failed',
                    'error_message' => 'Order link_uid is empty'
                ]);

            // Get orders with their service category
            $orders = Order::where('status', 'processing')
                ->where('refunded', false)
                ->whereNotNull('link')
                ->where('link', '!=', '')
                ->where('quantity', '>', 0)
                ->where('price', '>', 0)
                ->whereNotNull('service_id')
                ->whereNotNull('user_id')
                ->whereNotNull('link_uid')
                ->where('link_uid', '!=', '')
                ->with('service')
                ->get()
                ->groupBy(function ($order) {
                    return $order->service->category;
                });

            // Get total processing orders count
            $totalProcessingOrders = Order::where('status', 'processing')
                ->where('refunded', false)
                ->count();

            // Process Facebook orders
            if (isset($orders['facebook'])) {
                $result = $this->processFacebookOrders($pcProfile, $orders['facebook'], $totalProcessingOrders);
                if ($result['status'] === 'success') {
                    return response()->json($result);
                }
            }

            // Process Gmail orders
            if (isset($orders['gmail'])) {
                $result = $this->processGmailOrders($pcProfile, $orders['gmail'], $totalProcessingOrders);
                if ($result['status'] === 'success') {
                    return response()->json($result);
                }
            }

            // If we reach here, no orders could be processed
            return response()->json([
                'status' => 'no_orders',
                'message' => 'Not enough new orders available for any account',
                'max_order_limit' => $pcProfile->max_order_limit,
                'min_order_limit' => $pcProfile->min_order_limit,
                'total_pending_processing_orders' => $totalProcessingOrders,
                'data' => []
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getOrders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Process Facebook orders
     */
    private function processFacebookOrders(PcProfile $pcProfile, $orders, $totalProcessingOrders)
    {
        // Get all Facebook accounts with Chrome profiles for this PC profile that haven't been used
        $facebookAccounts = FacebookAccount::with('chromeProfile')
            ->where('pc_profile_id', $pcProfile->id)
            ->where('status', 'active')
            ->where('have_use', false)
            ->where('lang', 'en')
            ->whereHas('chromeProfile')  // Only get accounts that have a chrome profile
            ->get()
            // Filter out accounts that have reached their daily limit
            ->filter(function($account) {
                return !$account->hasReachedDailyLimit();
            });

        // If no unused accounts found, reset all accounts and try again
        if ($facebookAccounts->isEmpty()) {
            // Reset all active Facebook accounts for this PC profile
            FacebookAccount::where('pc_profile_id', $pcProfile->id)
                ->where('status', 'active')
                ->update(['have_use' => false]);

            // Get fresh list of accounts
            $facebookAccounts = FacebookAccount::where('pc_profile_id', $pcProfile->id)
                ->where('status', 'active')
                ->where('have_use', false)
                ->where('lang', 'en')
                ->whereHas('chromeProfile')
                ->get()
                // Filter out accounts that have reached their daily limit
                ->filter(function($account) {
                    return !$account->hasReachedDailyLimit();
                });

            if ($facebookAccounts->isEmpty()) {
                return [
                    'status' => 'error',
                    'message' => 'No active Facebook accounts found or all accounts have reached daily limit',
                    'total_pending_processing_orders' => $totalProcessingOrders
                ];
            }
        }

        // Check each Facebook account
        foreach ($facebookAccounts as $facebookAccount) {
            // Get existing order_link_uids for this account
            $existingUids = [];
            if ($facebookAccount->order_link_uid) {
                $existingUids = is_array($facebookAccount->order_link_uid) ? 
                    $facebookAccount->order_link_uid : 
                    json_decode($facebookAccount->order_link_uid, true);
            }

            // Filter orders not in this account's order_link_uid
            $availableOrders = $orders->filter(function ($order) use ($existingUids) {
                return !in_array($order->link_uid, $existingUids);
            })->take($pcProfile->max_order_limit);

            $orderCount = $availableOrders->count();

            // If we have enough new orders for this account
            if ($orderCount >= $pcProfile->min_order_limit) {
                // Start transaction to update Facebook account and orders
                DB::beginTransaction();
                try {
                    // Get new order_link_uids
                    $newOrderLinkUids = $availableOrders->pluck('link_uid')->toArray();
                    
                    // Merge with existing uids if any
                    $allOrderLinkUids = array_merge($existingUids, $newOrderLinkUids);
                    
                    // Update Facebook account's order_link_uid and have_use
                    $facebookAccount->order_link_uid = $allOrderLinkUids;
                    $facebookAccount->have_use = true;
                    
                    // Increment the use count
                    $facebookAccount->incrementUseCount();

                    // Update orders status to processing and decrement remains
                    $availableOrders->each(function ($order) {
                        $order->status = 'processing';
                        
                        // Initialize remains if null
                        if ($order->remains === null) {
                            $order->remains = $order->quantity;
                        }
                        
                        // Decrement remains by 1
                        if ($order->remains > 0) {
                            $order->remains--;
                        }

                        // If remains reaches 0, mark order as completed
                        if ($order->remains === 0) {
                            $order->status = 'completed';
                        }

                        $order->save();
                    });

                    DB::commit();

                    // Return success response
                    return [
                        'status' => 'success',
                        'message' => 'Facebook orders retrieved successfully',
                        'max_order_limit' => $pcProfile->max_order_limit,
                        'min_order_limit' => $pcProfile->min_order_limit,
                        'total_pending_processing_orders' => $totalProcessingOrders,
                        'category' => 'facebook',
                        'facebook_account' => [
                            'id' => $facebookAccount->id,
                            'email' => $facebookAccount->email,
                            'password' => $facebookAccount->password,
                            'status' => $facebookAccount->status,
                            'have_use' => $facebookAccount->have_use,
                            'have_page' => $facebookAccount->have_page,
                            'have_post' => $facebookAccount->have_post,
                            'lang' => $facebookAccount->lang,
                            'use_count' => $facebookAccount->use_count,
                            'account_cookies' => $facebookAccount->account_cookies,
                            'chrome_profile' => [
                                'id' => $facebookAccount->chromeProfile->id,
                                'profile_directory' => $facebookAccount->chromeProfile->profile_directory,
                                'user_agent' => $facebookAccount->chromeProfile->user_agent,
                                'status' => $facebookAccount->chromeProfile->status
                            ]
                        ],
                        'data' => $availableOrders->values()->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'user_id' => $order->user_id,
                                'service_id' => $order->service_id,
                                'link' => $order->link,
                                'link_uid' => $order->link_uid,
                                'quantity' => $order->quantity,
                                'status' => $order->status,
                                'remains' => $order->remains,
                                'category' => $order->service->category,
                                'error_message' => $order->error_message
                            ];
                        })->all()
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        }

        return [
            'status' => 'error',
            'message' => 'Not enough Facebook orders available',
            'total_pending_processing_orders' => $totalProcessingOrders
        ];
    }

    /**
     * Process Gmail orders
     */
    private function processGmailOrders(PcProfile $pcProfile, $orders, $totalProcessingOrders)
    {
        // Get all Gmail accounts with Chrome profiles for this PC profile that haven't been used
        $gmailAccounts = GmailAccount::with('chromeProfile')
            ->where('pc_profile_id', $pcProfile->id)
            ->where('status', 'active')
            ->where('have_use', false)
            ->whereHas('chromeProfile')  // Only get accounts that have a chrome profile
            ->get();

        // If no unused accounts found, reset all accounts and try again
        if ($gmailAccounts->isEmpty()) {
            // Reset all active Gmail accounts for this PC profile
            GmailAccount::where('pc_profile_id', $pcProfile->id)
                ->where('status', 'active')
                ->update(['have_use' => false]);

            // Get fresh list of accounts
            $gmailAccounts = GmailAccount::where('pc_profile_id', $pcProfile->id)
                ->where('status', 'active')
                ->where('have_use', false)
                ->whereHas('chromeProfile')
                ->get();

            if ($gmailAccounts->isEmpty()) {
                return [
                    'status' => 'error',
                    'message' => 'No active Gmail accounts found',
                    'total_pending_processing_orders' => $totalProcessingOrders
                ];
            }
        }

        // Check each Gmail account
        foreach ($gmailAccounts as $gmailAccount) {
            // Get existing order_link_uids for this account
            $existingUids = [];
            if ($gmailAccount->order_link_uid) {
                $existingUids = is_array($gmailAccount->order_link_uid) ? 
                    $gmailAccount->order_link_uid : 
                    json_decode($gmailAccount->order_link_uid, true);
            }

            // Filter orders not in this account's order_link_uid
            $availableOrders = $orders->filter(function ($order) use ($existingUids) {
                return !in_array($order->link_uid, $existingUids);
            })->take($pcProfile->max_order_limit);

            $orderCount = $availableOrders->count();

            // If we have enough new orders for this account
            if ($orderCount >= $pcProfile->min_order_limit) {
                // Start transaction to update Gmail account and orders
                DB::beginTransaction();
                try {
                    // Get new order_link_uids
                    $newOrderLinkUids = $availableOrders->pluck('link_uid')->toArray();
                    
                    // Merge with existing uids if any
                    $allOrderLinkUids = array_merge($existingUids, $newOrderLinkUids);
                    
                    // Update Gmail account's order_link_uid and have_use
                    $gmailAccount->order_link_uid = $allOrderLinkUids;
                    $gmailAccount->have_use = true;
                    $gmailAccount->save();

                    // Update orders status to processing and decrement remains
                    $availableOrders->each(function ($order) {
                        $order->status = 'processing';
                        
                        // Initialize remains if null
                        if ($order->remains === null) {
                            $order->remains = $order->quantity;
                        }
                        
                        // Decrement remains by 1
                        if ($order->remains > 0) {
                            $order->remains--;
                        }

                        // If remains reaches 0, mark order as completed
                        if ($order->remains === 0) {
                            $order->status = 'completed';
                        }

                        $order->save();
                    });

                    DB::commit();

                    // Return success response
                    return [
                        'status' => 'success',
                        'message' => 'Gmail orders retrieved successfully',
                        'max_order_limit' => $pcProfile->max_order_limit,
                        'min_order_limit' => $pcProfile->min_order_limit,
                        'total_pending_processing_orders' => $totalProcessingOrders,
                        'category' => 'gmail',
                        'gmail_account' => [
                            'id' => $gmailAccount->id,
                            'email' => $gmailAccount->email,
                            'password' => $gmailAccount->password,
                            'status' => $gmailAccount->status,
                            'have_use' => $gmailAccount->have_use,
                            'chrome_profile' => [
                                'id' => $gmailAccount->chromeProfile->id,
                                'profile_directory' => $gmailAccount->chromeProfile->profile_directory,
                                'user_agent' => $gmailAccount->chromeProfile->user_agent,
                                'status' => $gmailAccount->chromeProfile->status
                            ]
                        ],
                        'data' => $availableOrders->values()->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'user_id' => $order->user_id,
                                'service_id' => $order->service_id,
                                'link' => $order->link,
                                'link_uid' => $order->link_uid,
                                'quantity' => $order->quantity,
                                'status' => $order->status,
                                'remains' => $order->remains,
                                'category' => $order->service->category,
                                'error_message' => $order->error_message
                            ];
                        })->all()
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        }

        return [
            'status' => 'error',
            'message' => 'Not enough Gmail orders available',
            'total_pending_processing_orders' => $totalProcessingOrders
        ];
    }
} 