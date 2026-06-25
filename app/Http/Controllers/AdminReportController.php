<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.reports', [
            'ordersByStatus' => Order::query()
                ->select('status', DB::raw('count(*) as aggregate'))
                ->groupBy('status')
                ->orderBy('status')
                ->pluck('aggregate', 'status'),
            'paymentsByStatus' => Payment::query()
                ->select('status', DB::raw('count(*) as aggregate'))
                ->groupBy('status')
                ->orderBy('status')
                ->pluck('aggregate', 'status'),
            'topCleanersByOrders' => Cleaner::query()
                ->withCount('orders')
                ->orderByDesc('orders_count')
                ->take(5)
                ->get(),
            'topCleanersByRevenue' => Cleaner::query()
                ->select('cleaners.*')
                ->selectSub(
                    Order::query()
                        ->selectRaw('COALESCE(SUM(total), 0)')
                        ->whereColumn('orders.cleaner_id', 'cleaners.id')
                        ->where('payment_status', 'paid'),
                    'paid_revenue',
                )
                ->orderByDesc('paid_revenue')
                ->take(5)
                ->get(),
            'mostReviewedCleaners' => Cleaner::query()
                ->withCount('reviews')
                ->orderByDesc('reviews_count')
                ->take(5)
                ->get(),
            'recentPaidOrders' => Order::query()
                ->with(['customer', 'cleaner'])
                ->where('payment_status', 'paid')
                ->latest('paid_at')
                ->take(8)
                ->get(),
            'recentCancelledOrders' => Order::query()
                ->with(['customer', 'cleaner'])
                ->where('status', 'cancelled')
                ->latest()
                ->take(8)
                ->get(),
            'totalReviews' => Review::query()->count(),
            'averageRating' => Review::query()->avg('rating') ?? 0,
        ]);
    }
}
