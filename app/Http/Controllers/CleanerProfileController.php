<?php

namespace App\Http\Controllers;

use App\Http\Requests\CleanerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleanerProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('cleaner.profile.edit', [
            'cleaner' => $request->user()->cleaner,
        ]);
    }

    public function store(CleanerProfileRequest $request): RedirectResponse
    {
        if ($request->user()->cleaner()->exists()) {
            return redirect()
                ->route('cleaner.profile.edit')
                ->with('error', 'Your cleaner profile already exists. Update it below.');
        }

        $request->user()->cleaner()->create($request->profileData() + [
            'is_approved' => false,
        ]);

        return redirect()
            ->route('cleaner.profile.edit')
            ->with('success', 'Cleaner profile submitted. An admin will review it soon.');
    }

    public function update(CleanerProfileRequest $request): RedirectResponse
    {
        $cleaner = $request->user()->cleaner;

        if ($cleaner) {
            $cleaner->update($request->profileData());

            return redirect()
                ->route('cleaner.profile.edit')
                ->with('success', 'Cleaner profile updated.');
        }

        $request->user()->cleaner()->create($request->profileData() + [
            'is_approved' => false,
        ]);

        return redirect()
            ->route('cleaner.profile.edit')
            ->with('success', 'Cleaner profile created. An admin will review it soon.');
    }
}
