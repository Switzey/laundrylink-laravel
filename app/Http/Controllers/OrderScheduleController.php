<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderScheduleController extends Controller
{
    public const TIME_WINDOWS = [
        '8am - 10am',
        '10am - 12pm',
        '12pm - 2pm',
        '2pm - 4pm',
        '4pm - 6pm',
    ];

    public function edit(Request $request, Order $order): View
    {
        $this->authorizeReschedule($request, $order);

        return view('orders.reschedule', [
            'order' => $order,
            'timeWindows' => self::TIME_WINDOWS,
        ]);
    }

    public function update(Request $request, Order $order, NotificationService $notifications, OrderActivityService $activities): RedirectResponse
    {
        $this->authorizeReschedule($request, $order);

        $validated = $this->validatedSchedule($request);

        $order->update($validated);

        $activities->log(
            $order,
            'schedule_updated',
            'Customer requested an updated pickup and delivery schedule.',
            $request->user(),
            $validated,
        );

        if ($order->cleaner?->user) {
            $notifications->create(
                $order->cleaner->user,
                'Order schedule updated',
                $request->user()->name.' updated the schedule for order #'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT).'.',
                'order_status_updated',
                ['order_id' => $order->id],
            );
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order schedule updated.');
    }

    private function authorizeReschedule(Request $request, Order $order): void
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        if (in_array($order->status, ['completed', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'schedule' => 'Completed or cancelled orders cannot be rescheduled.',
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedSchedule(Request $request): array
    {
        return $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time_window' => ['required', Rule::in(self::TIME_WINDOWS)],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
            'delivery_time_window' => ['nullable', Rule::in(self::TIME_WINDOWS)],
            'pickup_notes' => ['nullable', 'string', 'max:1000'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
