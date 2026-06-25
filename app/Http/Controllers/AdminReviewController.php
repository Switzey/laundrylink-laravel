<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.reviews.index', [
            'reviews' => Review::query()
                ->with(['customer', 'cleaner', 'order'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function destroy(Review $review): RedirectResponse
    {
        $cleaner = $review->cleaner;

        $review->delete();

        if ($cleaner) {
            Review::refreshCleanerRating($cleaner);
        }

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review deleted and cleaner rating recalculated.');
    }
}
