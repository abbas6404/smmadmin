<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\ManualPayment;
use App\Models\FacebookAccount;
use App\Models\GmailAccount;
use App\Models\ChromeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active accounts counts
        $activeGmailAccounts = GmailAccount::where('status', 'active')->count();
        $activeFacebookAccounts = FacebookAccount::where('status', 'active')->count();
        $activeChromeProfiles = ChromeProfile::where('status', 'active')->count();
        
        // Get total users
        $totalUsers = User::count();
        
        // Get total orders
        $totalOrders = Order::count();
        
        // Get total revenue (sum of all order amounts)
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        
        // Get pending orders
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Get processing orders
        $processingOrders = Order::where('status', 'processing')->count();
        
        // Get completed orders
        $completedOrders = Order::where('status', 'completed')->count();
        
        // Get cancelled orders
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        // Get total services
        $totalServices = Service::where('status', 'active')->count();
        
        // Get pending payments
        $pendingPayments = ManualPayment::where('status', 'pending')->count();
        
        // Get recent orders
        $recentOrders = Order::with(['user', 'service'])
            ->latest()
            ->take(10)
            ->get();
            
        // Get recent payments
        $recentPayments = ManualPayment::with('user')
            ->latest()
            ->take(10)
            ->get();
            
        // Get recent pending payments specifically
        $pendingPaymentsList = ManualPayment::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
            
        // Get monthly revenue data for chart
        $monthlyRevenue = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get daily completed orders for the last 7 days
        $dailyCompletedOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get today's completed orders
        $todayCompletedOrders = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        // Get today's completed quantity
        $todayCompletedQuantity = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('quantity');

        // Get yesterday's completed orders
        $yesterdayCompletedOrders = Order::where('status', 'completed')
            ->whereDate('created_at', today()->subDay())
            ->count();

        // Get yesterday's completed quantity
        $yesterdayCompletedQuantity = Order::where('status', 'completed')
            ->whereDate('created_at', today()->subDay())
            ->sum('quantity');

        // Calculate completion rate
        $completionRate = $totalOrders > 0 
            ? round(($completedOrders / $totalOrders) * 100, 2)
            : 0;

        return view('backend.dashboard', compact(
            'admin',
            'totalUsers',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalServices',
            'pendingPayments',
            'recentOrders',
            'recentPayments',
            'pendingPaymentsList',
            'monthlyRevenue',
            'dailyCompletedOrders',
            'todayCompletedOrders',
            'todayCompletedQuantity',
            'yesterdayCompletedOrders',
            'yesterdayCompletedQuantity',
            'completionRate',
            'activeGmailAccounts',
            'activeFacebookAccounts',
            'activeChromeProfiles'
        ));
    }
} 