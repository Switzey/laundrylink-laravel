<?php

namespace App\Http\Controllers;

use App\Http\Requests\CleanerServiceRequest;
use App\Models\Cleaner;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerServiceController extends Controller
{
    public function index(Request $request): View
    {
        $cleaner = $request->user()->cleaner;

        $services = $cleaner
            ? $cleaner->services()->orderByDesc('is_active')->orderBy('name')->get()
            : collect();

        return view('cleaner.services.index', [
            'cleaner' => $cleaner,
            'services' => $services,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $cleaner = $this->cleanerFor($request);

        if (! $cleaner) {
            return redirect()
                ->route('cleaner.profile.edit')
                ->with('error', 'Create your cleaner profile before adding services.');
        }

        return view('cleaner.services.create', [
            'cleaner' => $cleaner,
            'service' => new Service(['is_active' => true, 'unit' => 'per_item']),
        ]);
    }

    public function store(CleanerServiceRequest $request): RedirectResponse
    {
        $cleaner = $this->cleanerFor($request);

        if (! $cleaner) {
            return redirect()
                ->route('cleaner.profile.edit')
                ->with('error', 'Create your cleaner profile before adding services.');
        }

        $cleaner->services()->create($request->serviceData());

        return redirect()
            ->route('cleaner.services.index')
            ->with('success', 'Service created.');
    }

    public function edit(Request $request, Service $service): View
    {
        $this->authorizeService($request, $service);

        return view('cleaner.services.edit', [
            'cleaner' => $request->user()->cleaner,
            'service' => $service,
        ]);
    }

    public function update(CleanerServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorizeService($request, $service);

        $service->update($request->serviceData());

        return redirect()
            ->route('cleaner.services.index')
            ->with('success', 'Service updated.');
    }

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeService($request, $service);

        if ($service->orderItems()->exists()) {
            $service->update(['is_active' => false]);

            return redirect()
                ->route('cleaner.services.index')
                ->with('success', 'This service has order history, so it was deactivated instead of deleted.');
        }

        $service->delete();

        return redirect()
            ->route('cleaner.services.index')
            ->with('success', 'Service deleted.');
    }

    private function cleanerFor(Request $request): ?Cleaner
    {
        return $request->user()->cleaner;
    }

    private function authorizeService(Request $request, Service $service): void
    {
        $cleaner = $this->cleanerFor($request);

        abort_unless($cleaner && $service->cleaner_id === $cleaner->id, 403);
    }
}
