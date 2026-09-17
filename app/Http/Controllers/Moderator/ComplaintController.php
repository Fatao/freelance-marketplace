<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['author', 'reviewedBy'])
            ->orderByRaw("FIELD(status, 'pending', 'reviewed', 'resolved', 'dismissed')")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('moderator.complaints.index', compact('complaints'));
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $request->validate(['resolution' => 'nullable|string|max:500']);

        $complaint->update([
            'status'      => 'resolved',
            'reviewed_by' => Auth::id(),
            'resolution'  => $request->resolution,
        ]);

        return back()->with('success', 'Жалоба решена.');
    }

    public function dismiss(Request $request, Complaint $complaint)
    {
        $request->validate(['resolution' => 'nullable|string|max:500']);

        $complaint->update([
            'status'      => 'dismissed',
            'reviewed_by' => Auth::id(),
            'resolution'  => $request->resolution,
        ]);

        return back()->with('success', 'Жалоба отклонена.');
    }
}