<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLogisticsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filter = $request->query('filter', 'upcoming');
        $today = today();

        $orders = Order::query()
            ->with(['customer', 'cleaner'])
            ->when($filter === 'today', function ($query) use ($today): void {
                $query->where(function ($query) use ($today): void {
                    $query->whereDate('pickup_date', $today)
                        ->orWhereDate('delivery_date', $today);
                });
            })
            ->when($filter === 'upcoming', function ($query) use ($today): void {
                $query->whereNotIn('status', ['completed', 'cancelled'])
                    ->where(function ($query) use ($today): void {
                        $query->whereDate('pickup_date', '>=', $today)
                            ->orWhereDate('delivery_date', '>=', $today);
                    });
            })
            ->when($filter === 'completed', fn ($query) => $query->where('status', 'completed'))
            ->when($filter === 'cancelled', fn ($query) => $query->where('status', 'cancelled'))
            ->latest('pickup_date')
            ->take(50)
            ->get();

        return view('admin.logistics', [
            'orders' => $orders,
            'filter' => $filter,
            'filters' => ['today', 'upcoming', 'completed', 'cancelled'],
        ]);
    }
}
