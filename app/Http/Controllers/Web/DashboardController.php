<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Party;
use App\Models\Payment;
use App\Models\SyncLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        // Core KPIs
        $stats = [
            'total_revenue' => Invoice::sum('total_amount'),
            'total_orders' => Order::count(),
            'total_invoices' => Invoice::count(),
            'total_products' => Product::count(),
            'total_customers' => Party::where('type', 'Customer')->count(),
            'total_users' => User::count(),
        ];

        // Revenue Trend (Last 6 Months)
        $revenueTrend = Invoice::select(
            DB::raw('SUM(total_amount) as total'),
            DB::raw("DATE_FORMAT(invoice_date, '%b %Y') as month")
        )
        ->groupBy('month')
        ->orderBy(DB::raw('MIN(invoice_date)'), 'desc')
        ->take(6)
        ->get()
        ->reverse();

        // Order Status Distribution
        $orderStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent Activities
        $recentOrders = Order::with('party')->latest()->take(5)->get();
        $recentPayments = Payment::with('party')->latest()->take(5)->get();
        $recentSyncLogs = SyncLog::latest()->take(5)->get();
        $recentLogins = LoginLog::with('user')->latest()->take(5)->get();

        // Activity History from Spatie Activity Log
        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(15)
            ->get();

        // Low Stock Alerts
        $lowStockProducts = Product::take(5)->get();

        return view('dashboard', compact(
            'stats', 
            'revenueTrend', 
            'orderStatus', 
            'recentOrders', 
            'recentPayments', 
            'recentSyncLogs', 
            'recentLogins',
            'lowStockProducts',
            'recentActivities'
        ));
    }
}
