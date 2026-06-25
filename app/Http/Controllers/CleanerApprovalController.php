<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;

class CleanerApprovalController extends Controller
{
    public function approve(Cleaner $cleaner, NotificationService $notifications): RedirectResponse
    {
        $cleaner->update(['is_approved' => true]);

        if ($cleaner->user) {
            $notifications->create(
                $cleaner->user,
                'Cleaner profile approved',
                $cleaner->business_name.' is now approved and visible to customers when available.',
                'cleaner_approved',
                ['cleaner_id' => $cleaner->id],
            );
        }

        return back()->with('success', "{$cleaner->business_name} has been approved.");
    }

    public function unapprove(Cleaner $cleaner): RedirectResponse
    {
        $cleaner->update(['is_approved' => false]);

        return back()->with('success', "{$cleaner->business_name} has been marked as not approved.");
    }
}
