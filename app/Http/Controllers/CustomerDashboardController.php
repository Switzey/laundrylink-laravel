<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $availableFilters = ['all', 'pending', 'active', 'completed', 'cancelled', 'paid', 'unpaid'];
        $currentFilter = in_array($request->query('filter'), $availableFilters, true)
            ? $request->query('filter')
            : 'all';

        $orders = $this->filteredOrders(
            Order::query()
                ->where('customer_id', $user->id)
                ->with(['cleaner', 'payment', 'review']),
            $currentFilter,
        )
            ->latest()
            ->take(12)
            ->get();

        $allOrders = Order::query()
            ->where('customer_id', $user->id)
            ->get(['status', 'payment_status']);

        $reviewableOrders = Order::query()
            ->where('customer_id', $user->id)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereDoesntHave('review')
            ->with('cleaner')
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Order::query()
            ->where('customer_id', $user->id)
            ->with(['activities' => fn ($query) => $query->latest()->take(1), 'cleaner'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->pluck('activities')
            ->flatten();

        return view('customer.dashboard', [
            'stats' => [
                'total_orders' => $allOrders->count(),
                'active_orders' => $allOrders->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'completed_orders' => $allOrders->where('status', 'completed')->count(),
            ],
            'orders' => $orders,
            'orderFilters' => $availableFilters,
            'currentFilter' => $currentFilter,
            'reviewableOrders' => $reviewableOrders,
            'unreadNotifications' => $user->appNotifications()
                ->whereNull('read_at')
                ->latest()
                ->take(5)
                ->get(),
            'recentActivities' => $recentActivities,
        ]);
    }

    private function filteredOrders($query, string $filter)
    {
        return match ($filter) {
            'pending' => $query->where('status', 'pending'),
            'active' => $query->whereNotIn('status', ['completed', 'cancelled']),
            'completed' => $query->where('status', 'completed'),
            'cancelled' => $query->where('status', 'cancelled'),
            'paid' => $query->where('payment_status', 'paid'),
            'unpaid' => $query->where('payment_status', '!=', 'paid'),
            default => $query,
        };
    }
}
