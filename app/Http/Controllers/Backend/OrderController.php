<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'service']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order ID, user name, or link
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('link', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

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
            ->paginate(25);
            
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
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        
        // If order is cancelled, set total_amount to 0
        if ($request->status === 'cancelled') {
            $order->total_amount = 0;
        }
        
        $order->save();

        return back()->with('success', 'Order status updated successfully');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        
        foreach ($orders as $order) {
            $order->status = $request->status;
            if ($request->status === 'cancelled') {
                $order->total_amount = 0;
            }
            $order->save();
        }

        return response()->json(['success' => true, 'message' => 'Orders updated successfully']);
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'service']);

        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('link', 'LIKE', "%{$search}%");
            });
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
} 