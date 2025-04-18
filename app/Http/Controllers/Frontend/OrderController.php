<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['service']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('link', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Sort orders
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        $orders = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Order::where('user_id', auth()->id())->count(),
            'pending' => Order::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', auth()->id())->where('status', 'processing')->count(),
            'completed' => Order::where('user_id', auth()->id())->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', auth()->id())->where('status', 'cancelled')->count(),
        ];

        return view('frontend.orders.index', compact('orders', 'stats'));
    }

    public function create(Service $service)
    {
        if ($service->status !== 'active') {
            return redirect()->route('services')->with('error', 'This service is currently unavailable.');
        }

        return view('frontend.orders.create', compact('service'));
    }

    public function store(Request $request, Service $service)
    {
        try {
            if ($service->status !== 'active') {
                return redirect()->route('services')->with('error', 'This service is currently unavailable.');
            }

            $request->validate([
                'link' => 'required|url',
                'quantity' => [
                    'required',
                    'integer',
                    'min:' . $service->min_quantity,
                    'max:' . $service->max_quantity,
                ],
                'description' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Check if user has sufficient balance
            $totalAmount = ($service->price * $request->quantity) / 1000;
            if (auth()->user()->balance < $totalAmount) {
                return back()->with('error', 'Insufficient balance. Please add funds to your account.');
            }

            // Create the order
            $orderData = [
                'user_id' => auth()->id(),
                'service_id' => $service->id,
                'link' => $request->link,
                'quantity' => $request->quantity,
                'price' => $service->price,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'description' => $request->description,
                'remains' => $request->quantity, // Set initial remains equal to quantity
            ];

            $order = Order::create($orderData);

            if (!$order) {
                throw new \Exception('Failed to create order');
            }

            // Deduct amount from user's balance
            auth()->user()->decrement('balance', $totalAmount);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Your balance has been updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while processing your order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('services')->with('error', 'Order not found.');
        }

        return view('frontend.orders.show', compact('order'));
    }

    public function updateStatus(Order $order, Request $request)
    {
        try {
            // Ensure the order belongs to the authenticated user
            if ($order->user_id !== auth()->id()) {
                return response()->json(['error' => 'You are not authorized to update this order'], 403);
            }

            // Validate the status
            $request->validate([
                'status' => ['required', 'string', 'in:pending,processing,completed,cancelled']
            ]);

            // Check if status is already completed or cancelled
            if (in_array($order->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'error' => 'Cannot update status of completed or cancelled orders'
                ], 422);
            }

            // Check if the new status is the same as current status
            if ($order->status === $request->status) {
                return response()->json([
                    'error' => 'Order is already in this status'
                ], 422);
            }

            DB::beginTransaction();

            try {
                $oldStatus = $order->status;
                
                // Handle refund if order is being cancelled
                if ($request->status === 'cancelled' && in_array($oldStatus, ['pending', 'processing'])) {
                    Log::info('Starting order cancellation process:', [
                        'order_id' => $order->id,
                        'old_status' => $oldStatus,
                        'user_id' => auth()->id()
                    ]);

                    // Load fresh user data to prevent race conditions
                    $user = $order->user()->lockForUpdate()->first();
                    if (!$user) {
                        throw new \Exception('User not found for refund processing');
                    }
                    
                    // Calculate refund amount
                    $refundAmount = $order->total_amount;
                    
                    // Process refund
                    $previousBalance = $user->balance;
                    $user->balance += $refundAmount;
                    $user->save();

                    Log::info('Refund processed:', [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'refund_amount' => $refundAmount,
                        'previous_balance' => $previousBalance,
                        'new_balance' => $user->balance
                    ]);

                    // Add balance history record for refund
                    $user->recordBalanceChange(
                        $refundAmount,
                        'credit',
                        'Refund for cancelled order #' . $order->id,
                        'order_refund_' . $order->id
                    );
                    
                    $order->refunded = true;
                    $order->total_amount = 0; // Set total_amount to 0 for cancelled orders
                }

                // Update order status
                $order->status = $request->status;
                $order->save();

                DB::commit();

                Log::info('Order status updated successfully:', [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'refunded' => $order->refunded ?? false
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully',
                    'order' => $order->fresh()
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction failed during order status update:', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (ValidationException $e) {
            Log::warning('Order status validation failed:', [
                'order_id' => $order->id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'error' => $e->errors()['status'][0] ?? 'Invalid status provided'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order status update failed:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function massCreate(Service $service)
    {
        if ($service->status !== 'active') {
            return redirect()->route('services')->with('error', 'This service is currently unavailable.');
        }

        return view('frontend.orders.create-mass', compact('service'));
    }

    public function massStore(Request $request, Service $service)
    {
        try {
            if ($service->status !== 'active') {
                return redirect()->route('services')->with('error', 'This service is currently unavailable.');
            }

            $request->validate([
                'links' => 'required|string',
                'quantity' => [
                    'required',
                    'integer',
                    'min:' . $service->min_quantity,
                    'max:' . $service->max_quantity,
                ],
                'description' => 'nullable|string|max:1000',
            ]);

            // Split links by newline and filter out empty lines
            $links = array_filter(explode("\n", $request->links), function($link) {
                return trim($link) !== '';
            });

            if (empty($links)) {
                return back()->with('error', 'Please provide at least one valid link.');
            }

            DB::beginTransaction();

            // Calculate total amount for all orders
            $totalAmount = ($service->price * $request->quantity * count($links)) / 1000;
            
            // Check if user has sufficient balance
            if (auth()->user()->balance < $totalAmount) {
                return back()->with('error', 'Insufficient balance. Please add funds to your account.');
            }

            $orders = [];
            foreach ($links as $link) {
                $link = trim($link);
                if (!filter_var($link, FILTER_VALIDATE_URL)) {
                    continue;
                }

                $orderData = [
                    'user_id' => auth()->id(),
                    'service_id' => $service->id,
                    'link' => $link,
                    'quantity' => $request->quantity,
                    'price' => $service->price,
                    'total_amount' => ($service->price * $request->quantity) / 1000,
                    'status' => 'pending',
                    'description' => $request->description,
                    'remains' => $request->quantity, // Set initial remains equal to quantity
                ];

                $order = Order::create($orderData);
                if ($order) {
                    $orders[] = $order;
                }
            }

            if (empty($orders)) {
                throw new \Exception('No valid orders were created.');
            }

            // Deduct total amount from user's balance
            auth()->user()->decrement('balance', $totalAmount);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', count($orders) . ' orders placed successfully! Your balance has been updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mass order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while processing your orders: ' . $e->getMessage());
        }
    }

    public function massDetails()
    {
        $orderIds = session('orderIds', []);
        if (empty($orderIds)) {
            return redirect()->route('orders.index')->with('error', 'No mass order details found.');
        }

        $orders = Order::whereIn('id', $orderIds)
            ->where('user_id', auth()->id())
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('orders.index')->with('error', 'No orders found.');
        }

        return view('frontend.orders.mass-details', compact('orders'));
    }
} 