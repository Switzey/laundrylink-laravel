@php
    $nextStatuses = [
        'pending' => ['accepted' => 'Accept'],
        'accepted' => ['picked_up' => 'Mark Picked Up'],
        'picked_up' => ['in_cleaning' => 'Mark In Cleaning'],
        'in_cleaning' => ['ready' => 'Mark Ready'],
        'ready' => ['out_for_delivery' => 'Out For Delivery'],
        'out_for_delivery' => ['completed' => 'Complete'],
    ][$order->status] ?? [];
@endphp

<div class="p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('orders.show', $order) }}" class="font-black text-neutral-950 hover:text-teal-800">
                Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}
            </a>
            <p class="mt-1 text-sm text-neutral-600">{{ $order->customer?->name ?? 'Guest customer' }} - NGN {{ number_format((float) $order->total, 2) }}</p>
            <p class="mt-1 text-xs text-neutral-500">{{ $order->created_at->format('M j, Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:justify-end">
            <x-status-badge :status="$order->status" />
            <x-payment-status-badge :status="$order->payment_status" />
        </div>
    </div>

    @unless ($compact)
        <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
            <div class="rounded-md bg-neutral-50 p-3">
                <dt class="text-neutral-500">Pickup</dt>
                <dd class="font-semibold text-neutral-900">{{ $order->pickup_address }}</dd>
                <dd class="mt-1 text-neutral-600">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}{{ $order->pickup_time_window ? ' - '.$order->pickup_time_window : '' }}</dd>
            </div>
            <div class="rounded-md bg-neutral-50 p-3">
                <dt class="text-neutral-500">Delivery</dt>
                <dd class="font-semibold text-neutral-900">{{ $order->delivery_address }}</dd>
                <dd class="mt-1 text-neutral-600">{{ $order->delivery_date?->format('M j, Y') ?? 'Not set' }}{{ $order->delivery_time_window ? ' - '.$order->delivery_time_window : '' }}</dd>
            </div>
        </dl>
    @endunless

    @if ($nextStatuses || $order->status === 'pending')
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($nextStatuses as $status => $label)
                <form method="POST" action="{{ route('cleaner.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" class="rounded-md bg-teal-700 px-3 py-2 text-xs font-bold text-white hover:bg-teal-800">{{ $label }}</button>
                </form>
            @endforeach

            @if ($order->status === 'pending')
                <form method="POST" action="{{ route('cleaner.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="rounded-md border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Cancel</button>
                </form>
            @endif
        </div>
    @endif
</div>
