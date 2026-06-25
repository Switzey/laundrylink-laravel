<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $cleaner = $request->user()->cleaner;
        $orders = $cleaner
            ? Order::query()->where('cleaner_id', $cleaner->id)
            : Order::query()->whereRaw('1 = 0');

        return view('cleaner.reports', [
            'cleaner' => $cleaner,
            'stats' => [
                'total_orders' => (clone $orders)->count(),
                'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
                'active_orders' => (clone $orders)->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'completed_orders' => (clone $orders)->where('status', 'completed')->count(),
                'cancelled_orders' => (clone $orders)->where('status', 'cancelled')->count(),
                'paid_orders' => (clone $orders)->where('payment_status', 'paid')->count(),
                'estimated_earnings' => (clone $orders)
                    ->where('status', 'completed')
                    ->where('payment_status', 'paid')
                    ->sum('total'),
            ],
            'recentPayments' => $cleaner
                ? Payment::query()
                    ->with(['order.customer', 'order.cleaner'])
                    ->whereHas('order', fn ($query) => $query->where('cleaner_id', $cleaner->id))
                    ->latest()
                    ->take(8)
                    ->get()
                : collect(),
            'mostOrderedServices' => $cleaner
                ? Service::query()
                    ->where('cleaner_id', $cleaner->id)
                    ->select('services.*')
                    ->selectSub(
                        OrderItem::query()
                            ->join('orders', 'orders.id', '=', 'order_items.order_id')
                            ->selectRaw('COALESCE(SUM(quantity), 0)')
                            ->whereColumn('order_items.service_id', 'services.id')
                            ->where('orders.cleaner_id', $cleaner->id),
                        'ordered_quantity',
                    )
                    ->orderByDesc('ordered_quantity')
                    ->take(8)
                    ->get()
                : collect(),
            'recentReviews' => $cleaner
                ? $cleaner->reviews()->with(['customer', 'order'])->latest()->take(8)->get()
                : collect(),
        ]);
    }
}
