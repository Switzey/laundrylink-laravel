<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\OrderActivity;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'total_users' => User::query()->count(),
                'total_customers' => User::query()->where('role', 'customer')->count(),
                'total_cleaners' => Cleaner::query()->count(),
                'approved_cleaners' => Cleaner::query()->where('is_approved', true)->count(),
                'pending_cleaners' => Cleaner::query()->where('is_approved', false)->count(),
                'total_orders' => Order::query()->count(),
                'pending_orders' => Order::query()->where('status', 'pending')->count(),
                'completed_orders' => Order::query()->where('status', 'completed')->count(),
                'cancelled_orders' => Order::query()->where('status', 'cancelled')->count(),
                'paid_orders' => Order::query()->where('payment_status', 'paid')->count(),
                'unpaid_orders' => Order::query()->where('payment_status', '!=', 'paid')->count(),
                'total_revenue' => Order::query()->where('payment_status', 'paid')->sum('total'),
                'total_platform_fees' => Order::query()->where('payment_status', 'paid')->sum('platform_fee'),
                'total_reviews' => Review::query()->count(),
                'average_platform_rating' => Review::query()->avg('rating') ?? 0,
            ],
            'pendingCleaners' => Cleaner::query()
                ->with('user')
                ->withCount('services')
                ->where('is_approved', false)
                ->latest()
                ->get(),
            'approvedCleaners' => Cleaner::query()
                ->with('user')
                ->withCount('services')
                ->where('is_approved', true)
                ->orderBy('business_name')
                ->get(),
            'recentOrders' => Order::query()
                ->with(['customer', 'cleaner'])
                ->latest()
                ->take(8)
                ->get(),
            'recentPayments' => Payment::query()
                ->with(['order.cleaner', 'customer'])
                ->latest()
                ->take(8)
                ->get(),
            'recentReviews' => Review::query()
                ->with(['customer', 'cleaner', 'order'])
                ->latest()
                ->take(6)
                ->get(),
            'recentActivities' => OrderActivity::query()
                ->with(['order.cleaner', 'user'])
                ->latest()
                ->take(8)
                ->get(),
            'unreadNotifications' => auth()->user()
                ->appNotifications()
                ->whereNull('read_at')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
