<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerController extends Controller
{
    public function index(Request $request): View
    {
        $sort = in_array($request->query('sort'), ['highest_rated', 'newest', 'fastest_turnaround'], true)
            ? $request->query('sort')
            : 'highest_rated';

        $cleanersQuery = Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->query('city')))
            ->when($request->filled('min_rating'), fn ($query) => $query->where('rating', '>=', (float) $request->query('min_rating')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->query('search');

                $query->where(function ($query) use ($search): void {
                    $query->where('business_name', 'like', '%'.$search.'%')
                        ->orWhere('city', 'like', '%'.$search.'%')
                        ->orWhereHas('services', function ($query) use ($search): void {
                            $query->where('is_active', true)
                                ->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->withCount(['services as services_count' => fn ($query) => $query->where('is_active', true)])
            ->withCount('reviews');

        $this->applySort($cleanersQuery, $sort);

        $cleaners = $cleanersQuery
            ->get();

        return view('cleaners.index', [
            'cleaners' => $cleaners,
            'cities' => Cleaner::query()
                ->where('is_approved', true)
                ->where('is_available', true)
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->pluck('city'),
            'filters' => [
                'city' => $request->query('city'),
                'search' => $request->query('search'),
                'min_rating' => $request->query('min_rating'),
                'sort' => $sort,
            ],
        ]);
    }

    public function show(Cleaner $cleaner): View
    {
        abort_unless($cleaner->is_approved && $cleaner->is_available, 404);

        $cleaner->load([
            'services' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
        ])->loadCount('reviews');

        $recentReviews = $cleaner->reviews()
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('cleaners.show', [
            'cleaner' => $cleaner,
            'recentReviews' => $recentReviews,
        ]);
    }

    public function storeProfile(Request $request): RedirectResponse
    {
        if ($request->user()->cleaner()->exists()) {
            return back()->with('error', 'Your cleaner profile already exists.');
        }

        $request->user()->cleaner()->create($this->validatedProfile($request) + [
            'is_approved' => false,
        ]);

        return back()->with('success', 'Cleaner profile submitted. An admin will review it soon.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $cleaner = $request->user()->cleaner;

        abort_unless($cleaner, 404);

        $cleaner->update($this->validatedProfile($request));

        return back()->with('success', 'Cleaner profile updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProfile(Request $request): array
    {
        return $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'turnaround_time' => ['nullable', 'string', 'max:120'],
        ]);
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'newest' => $query->latest(),
            'fastest_turnaround' => $query
                ->orderByRaw("CASE
                    WHEN lower(COALESCE(turnaround_time, '')) LIKE '%same day%' THEN 0
                    WHEN lower(COALESCE(turnaround_time, '')) LIKE '%24%' THEN 1
                    WHEN lower(COALESCE(turnaround_time, '')) LIKE '%48%' THEN 2
                    WHEN lower(COALESCE(turnaround_time, '')) LIKE '%72%' THEN 3
                    ELSE 4
                END")
                ->orderBy('turnaround_time')
                ->orderByDesc('rating'),
            default => $query->orderByDesc('rating')->latest(),
        };
    }
}
