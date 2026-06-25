<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerScheduleController extends Controller
{
    public function __invoke(Request $request): View
    {
        $cleaner = $request->user()->cleaner;
        $today = today();

        if (! $cleaner) {
            return view('cleaner.schedule', [
                'cleaner' => null,
                'pickupToday' => collect(),
                'upcomingPickups' => collect(),
                'deliveriesToday' => collect(),
                'upcomingDeliveries' => collect(),
            ]);
        }

        $baseQuery = Order::query()
            ->with(['customer', 'cleaner'])
            ->where('cleaner_id', $cleaner->id)
            ->whereNotIn('status', ['cancelled']);

        return view('cleaner.schedule', [
            'cleaner' => $cleaner,
            'pickupToday' => (clone $baseQuery)->whereDate('pickup_date', $today)->orderBy('pickup_time_window')->get(),
            'upcomingPickups' => (clone $baseQuery)->whereDate('pickup_date', '>', $today)->orderBy('pickup_date')->get(),
            'deliveriesToday' => (clone $baseQuery)->whereDate('delivery_date', $today)->orderBy('delivery_time_window')->get(),
            'upcomingDeliveries' => (clone $baseQuery)->whereDate('delivery_date', '>', $today)->orderBy('delivery_date')->get(),
        ]);
    }
}
