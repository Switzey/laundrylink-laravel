<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Services\NotificationService;
use App\Services\OrderActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request, Order $order): View
    {
        $this->ensureReviewable($request, $order);

        $order->load(['cleaner', 'items.service']);

        return view('reviews.create', [
            'order' => $order,
        ]);
    }

    public function store(Request $request, Order $order, NotificationService $notifications, OrderActivityService $activities): RedirectResponse
    {
        $this->ensureReviewable($request, $order);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $request, $validated, $activities, $notifications): void {
            $review = Review::query()->create([
                'order_id' => $order->id,
                'customer_id' => $request->user()->id,
                'cleaner_id' => $order->cleaner_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            Review::refreshCleanerRating($order->cleaner);

            $activities->log(
                $order,
                'review_received',
                $request->user()->name.' left a '.$review->rating.' star review.',
                $request->user(),
                ['review_id' => $review->id, 'rating' => $review->rating],
            );

            if ($order->cleaner?->user) {
                $notifications->create(
                    $order->cleaner->user,
                    'New customer review',
                    $request->user()->name.' left a '.$review->rating.' star review for order #'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT).'.',
                    'review_received',
                    ['order_id' => $order->id, 'review_id' => $review->id],
                );
            }
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Thanks for reviewing this cleaner.');
    }

    private function ensureReviewable(Request $request, Order $order): void
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        if (! $order->isReviewableBy($request->user())) {
            throw ValidationException::withMessages([
                'review' => 'This order is not eligible for review.',
            ]);
        }
    }
}
