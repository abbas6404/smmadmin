<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'service']);

        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order ID, user name, or link
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            
            // Check if search is numeric (likely an ID)
            if (is_numeric($search)) {
                $query->where('id', '=', $search);
            } else {
                // Safe search with bindings
                $query->where(function($q) use ($search) {
                    $searchTerm = '%' . $search . '%';
                    $q->where('link', 'LIKE', $searchTerm)
                      ->orWhere(function($subQuery) use ($searchTerm) {
                          $subQuery->whereHas('user', function($userQuery) use ($searchTerm) {
                              $userQuery->where('name', 'LIKE', $searchTerm);
                          });
                      })
                      ->orWhere(function($subQuery) use ($searchTerm) {
                          $subQuery->whereHas('service', function($serviceQuery) use ($searchTerm) {
                              $serviceQuery->where('name', 'LIKE', $searchTerm);
                          });
                      });
                });
            }
        }

        $orders = $query->orderBy('id', 'desc')->paginate(100)->withQueryString();

        // Get statistics
        $statistics = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'today' => Order::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('backend.orders.index', compact('orders', 'statistics'));
    }

    public function pending()
    {
        $orders = Order::with(['user', 'service'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(100);
            
        return redirect()->route('admin.orders.index', ['status' => 'pending']);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'service']);
        
        // Get related orders from same user
        $relatedOrders = Order::where('user_id', $order->user_id)
            ->where('id', '!=', $order->id)
            ->latest()
            ->limit(5)
            ->get();
            
        return view('backend.orders.show', compact('order', 'relatedOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'refund' => 'sometimes|boolean'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        
        // Handle refund when cancelling
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            // Check if refund is requested and order has amount to refund
            if ($request->has('refund') && $request->refund && $order->total_amount > 0) {
                try {
                    // Load user
                    $user = $order->user;
                    if ($user) {
                        // Start a transaction
                        DB::beginTransaction();
                        
                        // Get the refund amount
                        $refundAmount = $order->total_amount;
                        
                        // Add amount to user balance
                        $previousBalance = $user->balance;
                        $user->balance += $refundAmount;
                        $user->save();
                        
                        // Log the refund
                        Log::info('Order refunded from admin panel:', [
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'refund_amount' => $refundAmount,
                            'previous_balance' => $previousBalance,
                            'new_balance' => $user->balance
                        ]);
                        
                        // Add balance history record if method exists
                        if (method_exists($user, 'recordBalanceChange')) {
                            $user->recordBalanceChange(
                                $refundAmount,
                                'credit',
                                'Refund for cancelled order #' . $order->id,
                                'order_refund_' . $order->id
                            );
                        }
                        
                        // Mark order as refunded in notes or description field if available
                        if (Schema::hasColumn('orders', 'notes')) {
                            $order->notes = ($order->notes ? $order->notes . "\n" : '') . 
                                "[" . now()->format('Y-m-d H:i:s') . "] Refunded $" . number_format($refundAmount, 2) . " to user";
                        } elseif (Schema::hasColumn('orders', 'description')) {
                            $order->description = ($order->description ? $order->description . "\n" : '') . 
                                "[" . now()->format('Y-m-d H:i:s') . "] Refunded $" . number_format($refundAmount, 2) . " to user";
                        }
                        
                        // Set total amount to 0 to indicate it's been refunded
                        $order->total_amount = 0;
                        
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error refunding order:', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return back()->with('error', 'Error processing refund: ' . $e->getMessage());
                }
            } else {
                // If not refunding, just mark as cancelled
                $order->total_amount = 0;
            }
        }
        
        $order->save();

        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson() || $request->header('X-HTTP-Method-Override')) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully' . 
                    ($request->has('refund') && $request->refund ? ' with refund' : ''),
                'order' => $order
            ]);
        }

        // Regular request
        $successMessage = 'Order status updated successfully';
        if ($request->status === 'cancelled' && $request->has('refund') && $request->refund) {
            $successMessage .= ' with refund to user';
        }
        
        return back()->with('success', $successMessage);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'refund' => 'sometimes|boolean'
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        $refundedCount = 0;
        
        // Use a transaction for all refunds
        DB::beginTransaction();
        
        try {
            foreach ($orders as $order) {
                $oldStatus = $order->status;
                $order->status = $request->status;
                
                // Handle refund when cancelling
                if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
                    // Check if refund is requested and order has amount to refund
                    if ($request->has('refund') && $request->refund && $order->total_amount > 0) {
                        // Load user
                        $user = $order->user;
                        if ($user) {
                            // Get the refund amount
                            $refundAmount = $order->total_amount;
                            
                            // Add amount to user balance
                            $previousBalance = $user->balance;
                            $user->balance += $refundAmount;
                            $user->save();
                            
                            // Log the refund
                            Log::info('Order refunded from bulk operation:', [
                                'order_id' => $order->id,
                                'user_id' => $user->id,
                                'refund_amount' => $refundAmount,
                                'previous_balance' => $previousBalance,
                                'new_balance' => $user->balance
                            ]);
                            
                            // Add balance history record if method exists
                            if (method_exists($user, 'recordBalanceChange')) {
                                $user->recordBalanceChange(
                                    $refundAmount,
                                    'credit',
                                    'Refund for cancelled order #' . $order->id,
                                    'order_refund_' . $order->id
                                );
                            }
                            
                            // Mark order as refunded in notes or description field if available
                            if (Schema::hasColumn('orders', 'notes')) {
                                $order->notes = ($order->notes ? $order->notes . "\n" : '') . 
                                    "[" . now()->format('Y-m-d H:i:s') . "] Refunded $" . number_format($refundAmount, 2) . " to user";
                            } elseif (Schema::hasColumn('orders', 'description')) {
                                $order->description = ($order->description ? $order->description . "\n" : '') . 
                                    "[" . now()->format('Y-m-d H:i:s') . "] Refunded $" . number_format($refundAmount, 2) . " to user";
                            }
                            
                            $refundedCount++;
                            
                            // Set total amount to 0 to indicate it's been refunded
                            $order->total_amount = 0;
                        }
                    } else {
                        // If not refunding, just mark as cancelled
                        $order->total_amount = 0;
                    }
                }
                
                $order->save();
            }
            
            DB::commit();
            
            $message = 'Orders updated successfully';
            if ($refundedCount > 0) {
                $message .= " with {$refundedCount} refunds processed";
            }
            
            return response()->json(['success' => true, 'message' => $message]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing bulk order update with refunds:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Error updating orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'service']);

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by order ID, user name, or link
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            
            // Check if search is numeric (likely an ID)
            if (is_numeric($search)) {
                $query->where('id', '=', $search);
            } else {
                // Safe search with bindings
                $query->where(function($q) use ($search) {
                    $searchTerm = '%' . $search . '%';
                    $q->where('link', 'LIKE', $searchTerm)
                      ->orWhere(function($subQuery) use ($searchTerm) {
                          $subQuery->whereHas('user', function($userQuery) use ($searchTerm) {
                              $userQuery->where('name', 'LIKE', $searchTerm);
                          });
                      })
                      ->orWhere(function($subQuery) use ($searchTerm) {
                          $subQuery->whereHas('service', function($serviceQuery) use ($searchTerm) {
                              $serviceQuery->where('name', 'LIKE', $searchTerm);
                          });
                      });
                });
            }
        }

        $orders = $query->latest()->get();

        // Generate CSV
        $filename = 'orders-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Order ID',
                'User',
                'Service',
                'Link',
                'Start Count',
                'Remains',
                'Status',
                'Amount',
                'Created At'
            ]);

            // Add data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user ? $order->user->name : 'Deleted User',
                    $order->service ? $order->service->name : 'Deleted Service',
                    $order->link,
                    $order->start_count,
                    $order->remains,
                    ucfirst($order->status),
                    number_format($order->total_amount, 2),
                    $order->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateUid(Request $request, Order $order)
    {
        $request->validate([
            'link_uid' => 'required|string|max:255'
        ]);

        $order->link_uid = $request->link_uid;
        $order->save();

        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
            return response()->json([
                'success' => true,
                'message' => 'UID updated successfully',
                'order' => $order
            ]);
        }

        // Regular request
        return back()->with('success', 'Order UID updated successfully');
    }

    public function updateLink(Request $request, Order $order)
    {
        $request->validate([
            'link' => 'required|string|max:1000'
        ]);

        $order->link = $request->link;
        $order->save();

        return back()->with('success', 'Order link updated successfully');
    }

    public function updateStartCount(Request $request, Order $order)
    {
        $request->validate([
            'start_count' => 'required|integer|min:0'
        ]);

        $order->start_count = $request->start_count;
        $order->save();

        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson() || $request->header('X-HTTP-Method-Override')) {
            return response()->json([
                'success' => true,
                'message' => 'Start count updated successfully',
                'order' => $order
            ]);
        }

        // Regular request
        return back()->with('success', 'Start count updated successfully');
    }
} 