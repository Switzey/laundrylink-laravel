<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Service;
use App\Models\Address;
use App\Services\NotificationService;
use App\Services\OrderActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const TIME_WINDOWS = [
        '8am - 10am',
        '10am - 12pm',
        '12pm - 2pm',
        '2pm - 4pm',
        '4pm - 6pm',
    ];

    private const DELIVERY_FEE = 1500;

    private const PLATFORM_FEE = 500;

    /**
     * @var array<string, list<string>>
     */
    private const STATUS_FLOW = [
        'pending' => ['accepted', 'cancelled'],
        'accepted' => ['picked_up'],
        'picked_up' => ['in_cleaning'],
        'in_cleaning' => ['ready'],
        'ready' => ['out_for_delivery'],
        'out_for_delivery' => ['completed'],
    ];

    public function create(Request $request): View|RedirectResponse
    {
        $selectedCleaner = null;

        if ($request->filled('cleaner')) {
            $selectedCleaner = Cleaner::query()
                ->where('is_approved', true)
                ->where('is_available', true)
                ->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->find($request->integer('cleaner'));

            if (! $selectedCleaner) {
                return redirect()
                    ->route('cleaners.index')
                    ->with('error', 'That cleaner is not available for new orders right now.');
            }
        }

        $cleaners = Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->orderBy('business_name')
            ->get();

        return view('orders.create', [
            'cleaners' => $cleaners,
            'selectedCleaner' => $selectedCleaner,
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->orderBy('label')->get(),
            'defaultAddress' => $request->user()->addresses()->where('is_default', true)->first(),
            'timeWindows' => self::TIME_WINDOWS,
            'deliveryFee' => self::DELIVERY_FEE,
            'platformFee' => self::PLATFORM_FEE,
        ]);
    }

    public function store(Request $request, NotificationService $notifications, OrderActivityService $activities): RedirectResponse
    {
        $validated = $request->validate([
            'cleaner_id' => ['required', 'integer'],
            'services' => ['required', 'array'],
            'services.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'pickup_address_id' => ['nullable', 'integer'],
            'delivery_address_id' => ['nullable', 'integer'],
            'pickup_address' => ['nullable', 'string', 'max:255', 'required_without:pickup_address_id'],
            'delivery_address' => ['nullable', 'string', 'max:255', 'required_without:delivery_address_id'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time_window' => ['required', Rule::in(self::TIME_WINDOWS)],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
            'delivery_time_window' => ['nullable', Rule::in(self::TIME_WINDOWS)],
            'pickup_notes' => ['nullable', 'string', 'max:1000'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cleaner = Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->find($validated['cleaner_id']);

        if (! $cleaner) {
            throw ValidationException::withMessages([
                'cleaner_id' => 'Please choose an approved and available cleaner.',
            ]);
        }

        $quantities = collect($validated['services'])
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages([
                'services' => 'Please select at least one service quantity.',
            ]);
        }

        $services = Service::query()
            ->where('cleaner_id', $cleaner->id)
            ->where('is_active', true)
            ->whereIn('id', $quantities->keys()->map(fn ($id) => (int) $id))
            ->get()
            ->keyBy('id');

        if ($services->count() !== $quantities->count()) {
            throw ValidationException::withMessages([
                'services' => 'Please select active services that belong to the chosen cleaner.',
            ]);
        }

        $subtotal = $services->sum(fn (Service $service) => (float) $service->price * $quantities->get($service->id));
        $total = $subtotal + self::DELIVERY_FEE + self::PLATFORM_FEE;
        $pickupAddress = $this->customerAddress($request, $validated['pickup_address_id'] ?? null, 'pickup_address_id');
        $deliveryAddress = $this->customerAddress($request, $validated['delivery_address_id'] ?? null, 'delivery_address_id');
        $pickupAddressText = $pickupAddress ? $this->formatAddress($pickupAddress) : $validated['pickup_address'];
        $deliveryAddressText = $deliveryAddress ? $this->formatAddress($deliveryAddress) : $validated['delivery_address'];

        $order = DB::transaction(function () use ($request, $validated, $cleaner, $services, $quantities, $subtotal, $total, $pickupAddress, $deliveryAddress, $pickupAddressText, $deliveryAddressText): Order {
            $order = Order::query()->create([
                'customer_id' => $request->user()->id,
                'cleaner_id' => $cleaner->id,
                'pickup_address' => $pickupAddressText,
                'delivery_address' => $deliveryAddressText,
                'pickup_date' => $validated['pickup_date'],
                'pickup_time_window' => $validated['pickup_time_window'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time_window' => $validated['delivery_time_window'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => self::DELIVERY_FEE,
                'platform_fee' => self::PLATFORM_FEE,
                'total' => $total,
                'pickup_notes' => $validated['pickup_notes'] ?? $pickupAddress?->delivery_notes,
                'delivery_notes' => $validated['delivery_notes'] ?? $deliveryAddress?->delivery_notes,
                'notes' => $validated['notes'] ?? null,
            ]);

            $services->each(function (Service $service) use ($order, $quantities): void {
                $order->items()->create([
                    'service_id' => $service->id,
                    'quantity' => $quantities->get($service->id),
                    'price' => $service->price,
                ]);
            });

            return $order;
        });

        $activities->log(
            $order,
            'order_created',
            'Order was created by '.$request->user()->name.' and sent to '.$cleaner->business_name.'.',
            $request->user(),
            ['total' => $total],
        );

        $activities->log(
            $order,
            'schedule_created',
            'Pickup scheduled for '.$order->pickup_date?->format('M j, Y').' during '.$order->pickup_time_window.'.',
            $request->user(),
            [
                'pickup_date' => $order->pickup_date?->toDateString(),
                'pickup_time_window' => $order->pickup_time_window,
                'delivery_date' => $order->delivery_date?->toDateString(),
                'delivery_time_window' => $order->delivery_time_window,
            ],
        );

        if ($cleaner->user) {
            $notifications->create(
                $cleaner->user,
                'New laundry order',
                $request->user()->name.' created a new order with pickup scheduled for '.$order->pickup_date?->format('M j, Y').' during '.$order->pickup_time_window.'.',
                'order_created',
                ['order_id' => $order->id],
            );
        }

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Order created and sent to the cleaner.');
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrderAccess($request, $order);

        $order->load([
            'cleaner.services',
            'customer',
            'items.service',
            'payment',
            'review.customer',
            'activities' => fn ($query) => $query->with('user')->oldest(),
        ]);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order, NotificationService $notifications, OrderActivityService $activities): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'picked_up', 'in_cleaning', 'ready', 'out_for_delivery', 'completed', 'cancelled'])],
        ]);

        $cleaner = $request->user()->cleaner;

        abort_unless($cleaner && $cleaner->is($order->cleaner), 403);

        if (! $cleaner->is_approved) {
            return back()->with('error', 'Your cleaner profile must be approved before managing orders.');
        }

        $allowed = self::STATUS_FLOW[$order->status] ?? [];

        if (! in_array($validated['status'], $allowed, true)) {
            return back()->with('error', 'That status change is not allowed for this order.');
        }

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        $order->update(['status' => $newStatus]);

        $action = match ($newStatus) {
            'accepted' => 'order_accepted',
            'picked_up' => 'pickup_completed',
            'out_for_delivery' => 'delivery_started',
            'cancelled' => 'order_cancelled',
            'completed' => 'order_completed',
            default => 'order_status_updated',
        };

        $description = match ($newStatus) {
            'accepted' => $cleaner->business_name.' accepted the order.',
            'picked_up' => $cleaner->business_name.' marked the pickup as completed.',
            'out_for_delivery' => $cleaner->business_name.' marked the order as out for delivery.',
            'cancelled' => $cleaner->business_name.' cancelled the pending order.',
            'completed' => $cleaner->business_name.' marked the order as completed.',
            default => $cleaner->business_name.' updated the order status from '.str($oldStatus)->replace('_', ' ').' to '.str($newStatus)->replace('_', ' ').'.',
        };

        $activities->log($order, $action, $description, $request->user(), [
            'from' => $oldStatus,
            'to' => $newStatus,
        ]);

        if ($order->customer) {
            $notifications->create(
                $order->customer,
                $newStatus === 'accepted' ? 'Order accepted' : 'Order status updated',
                $description,
                $newStatus === 'accepted' ? 'order_accepted' : 'order_status_updated',
                ['order_id' => $order->id, 'status' => $newStatus],
            );
        }

        return back()->with('success', 'Order status updated.');
    }

    private function authorizeOrderAccess(Request $request, Order $order): void
    {
        $user = $request->user();

        if ($user->role === 'customer' && $order->customer_id === $user->id) {
            return;
        }

        if ($user->role === 'cleaner' && $user->cleaner?->is($order->cleaner)) {
            return;
        }

        if ($user->role === 'admin') {
            return;
        }

        abort(403);
    }

    private function customerAddress(Request $request, mixed $addressId, string $field): ?Address
    {
        if (! $addressId) {
            return null;
        }

        $address = $request->user()->addresses()->find($addressId);

        if (! $address) {
            throw ValidationException::withMessages([
                $field => 'Please choose one of your saved addresses.',
            ]);
        }

        return $address;
    }

    private function formatAddress(Address $address): string
    {
        return $address->address.', '.$address->city;
    }
}
