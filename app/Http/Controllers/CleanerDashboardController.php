<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $cleaner = $request->user()
            ->cleaner()
            ->withCount([
                'services as total_services',
                'services as active_services' => fn ($query) => $query->where('is_active', true),
                'reviews as total_reviews',
            ])
            ->first();

        $orders = $cleaner
            ? Order::query()
                ->where('cleaner_id', $cleaner->id)
                ->with(['customer', 'items.service', 'payment'])
                ->latest()
                ->get()
            : collect();

        return view('cleaner.dashboard', [
            'cleaner' => $cleaner,
            'incomingOrders' => $orders->whereIn('status', ['pending', 'accepted']),
            'recentOrders' => $orders->take(10),
            'recentReviews' => $cleaner
                ? $cleaner->reviews()->with(['customer', 'order'])->latest()->take(5)->get()
                : collect(),
            'unreadNotifications' => $request->user()
                ->appNotifications()
                ->whereNull('read_at')
                ->latest()
                ->take(5)
                ->get(),
            'recentActivities' => $cleaner
                ? $cleaner->orders()
                    ->with(['activities' => fn ($query) => $query->latest()->take(1)])
                    ->latest('updated_at')
                    ->take(5)
                    ->get()
                    ->pluck('activities')
                    ->flatten()
                : collect(),
            'stats' => [
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'active_orders' => $orders->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'earnings_estimate' => $orders->where('status', 'completed')->sum('total'),
                'total_services' => $cleaner?->total_services ?? 0,
                'active_services' => $cleaner?->active_services ?? 0,
                'total_reviews' => $cleaner?->total_reviews ?? 0,
            ],
        ]);
    }
}
