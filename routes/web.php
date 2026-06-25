<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminLogisticsController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\CleanerApprovalController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\CleanerDashboardController;
use App\Http\Controllers\CleanerProfileController;
use App\Http\Controllers\CleanerReportController;
use App\Http\Controllers\CleanerScheduleController;
use App\Http\Controllers\CleanerServiceController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Models\Cleaner;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredCleaners = Cleaner::query()
        ->where('is_approved', true)
        ->where('is_available', true)
        ->withCount(['services as services_count' => fn ($query) => $query->where('is_active', true)])
        ->withCount('reviews')
        ->orderByDesc('rating')
        ->take(3)
        ->get();

    return view('landing', [
        'featuredCleaners' => $featuredCleaners,
    ]);
})->name('home');

Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners.index');
Route::get('/cleaners/{cleaner}', [CleanerController::class, 'show'])->name('cleaners.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(match (auth()->user()->role) {
            'cleaner' => 'vendor.dashboard',
            'admin' => 'admin.dashboard',
            default => 'client.dashboard',
        });
    })->name('dashboard');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->whereNumber('notification')->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', CustomerDashboardController::class)->name('customer.dashboard');
    Route::get('/client/dashboard', CustomerDashboardController::class)->name('client.dashboard');
    Route::get('/customer/addresses', [CustomerAddressController::class, 'index'])->name('customer.addresses.index');
    Route::get('/customer/addresses/create', [CustomerAddressController::class, 'create'])->name('customer.addresses.create');
    Route::post('/customer/addresses', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
    Route::get('/customer/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])->whereNumber('address')->name('customer.addresses.edit');
    Route::match(['put', 'patch'], '/customer/addresses/{address}', [CustomerAddressController::class, 'update'])->whereNumber('address')->name('customer.addresses.update');
    Route::delete('/customer/addresses/{address}', [CustomerAddressController::class, 'destroy'])->whereNumber('address')->name('customer.addresses.destroy');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/reschedule', [OrderScheduleController::class, 'edit'])->whereNumber('order')->name('orders.reschedule.edit');
    Route::patch('/orders/{order}/reschedule', [OrderScheduleController::class, 'update'])->whereNumber('order')->name('orders.reschedule.update');
    Route::get('/orders/{order}/review', [ReviewController::class, 'create'])->whereNumber('order')->name('orders.review.create');
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->whereNumber('order')->name('orders.review.store');
});

Route::middleware(['auth', 'role:cleaner'])->prefix('cleaner')->name('cleaner.')->group(function () {
    Route::get('/dashboard', CleanerDashboardController::class)->name('dashboard');
    Route::get('/reports', CleanerReportController::class)->name('reports');
    Route::get('/schedule', CleanerScheduleController::class)->name('schedule');
    Route::get('/profile', [CleanerProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [CleanerProfileController::class, 'store'])->name('profile.store');
    Route::match(['put', 'patch'], '/profile', [CleanerProfileController::class, 'update'])->name('profile.update');
    Route::get('/services', [CleanerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [CleanerServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [CleanerServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [CleanerServiceController::class, 'edit'])->whereNumber('service')->name('services.edit');
    Route::match(['put', 'patch'], '/services/{service}', [CleanerServiceController::class, 'update'])->whereNumber('service')->name('services.update');
    Route::delete('/services/{service}', [CleanerServiceController::class, 'destroy'])->whereNumber('service')->name('services.destroy');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->whereNumber('order')->name('orders.status');
});

Route::middleware(['auth', 'role:cleaner'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', CleanerDashboardController::class)->name('dashboard');
    Route::get('/reports', CleanerReportController::class)->name('reports');
    Route::get('/schedule', CleanerScheduleController::class)->name('schedule');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/logistics', AdminLogisticsController::class)->name('logistics');
    Route::get('/reports', AdminReportController::class)->name('reports');
    Route::patch('/cleaners/{cleaner}/approve', [CleanerApprovalController::class, 'approve'])->name('cleaners.approve');
    Route::patch('/cleaners/{cleaner}/unapprove', [CleanerApprovalController::class, 'unapprove'])->name('cleaners.unapprove');
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->whereNumber('review')->name('reviews.destroy');
});

require __DIR__.'/auth.php';
