<?php

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredCleaners = Cleaner::query()
        ->where('is_approved', true)
        ->with('services')
        ->orderByDesc('rating')
        ->take(3)
        ->get();

    return view('landing', [
        'featuredCleaners' => $featuredCleaners,
    ]);
})->name('home');

Route::get('/customer/dashboard', function () {
    $orders = Order::query()
        ->with(['cleaner', 'items.service'])
        ->latest()
        ->take(6)
        ->get();

    return view('customer.dashboard', [
        'stats' => [
            'total_orders' => Order::query()->count(),
            'active_orders' => Order::query()->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'completed_orders' => Order::query()->where('status', 'completed')->count(),
        ],
        'orders' => $orders,
    ]);
})->name('customer.dashboard');

Route::get('/cleaners', function () {
    $cleaners = Cleaner::query()
        ->where('is_approved', true)
        ->withCount('services')
        ->orderByDesc('rating')
        ->get();

    return view('cleaners.index', [
        'cleaners' => $cleaners,
    ]);
})->name('cleaners.index');

Route::get('/cleaners/{cleaner}', function (Cleaner $cleaner) {
    $cleaner->load('services');

    return view('cleaners.show', [
        'cleaner' => $cleaner,
    ]);
})->name('cleaners.show');

Route::get('/orders/create', function (Request $request) {
    $selectedCleaner = null;

    if ($request->filled('cleaner')) {
        $selectedCleaner = Cleaner::query()
            ->with('services')
            ->find($request->integer('cleaner'));
    }

    $cleaners = Cleaner::query()
        ->where('is_approved', true)
        ->with('services')
        ->orderBy('business_name')
        ->get();

    return view('orders.create', [
        'cleaners' => $cleaners,
        'selectedCleaner' => $selectedCleaner,
    ]);
})->name('orders.create');

Route::get('/cleaner/dashboard', function () {
    $cleaner = Cleaner::query()
        ->where('is_approved', true)
        ->with('services')
        ->first();

    $orders = $cleaner
        ? Order::query()
            ->where('cleaner_id', $cleaner->id)
            ->with(['customer', 'items.service'])
            ->latest()
            ->take(8)
            ->get()
        : collect();

    return view('cleaner.dashboard', [
        'cleaner' => $cleaner,
        'incomingOrders' => $orders->whereIn('status', ['pending', 'accepted']),
        'recentOrders' => $orders,
    ]);
})->name('cleaner.dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard', [
        'stats' => [
            'total_users' => User::query()->count(),
            'total_cleaners' => Cleaner::query()->count(),
            'total_orders' => Order::query()->count(),
            'pending_cleaners' => Cleaner::query()->where('is_approved', false)->count(),
        ],
        'pendingCleaners' => Cleaner::query()
            ->where('is_approved', false)
            ->latest()
            ->get(),
        'recentOrders' => Order::query()
            ->with(['customer', 'cleaner'])
            ->latest()
            ->take(6)
            ->get(),
    ]);
})->name('admin.dashboard');
