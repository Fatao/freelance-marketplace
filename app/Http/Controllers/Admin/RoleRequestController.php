<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleRequest;
use App\Models\ClientProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRequestController extends Controller
{
    public function index()
    {
        $requests = RoleRequest::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at')
            ->paginate(20);

        $pendingCount = RoleRequest::where('status', 'pending')->count();

        return view('admin.role_requests.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, RoleRequest $roleRequest)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $user = $roleRequest->user;

        // Change role
        $user->update(['role' => 'client']);

        // Create client profile if not exists
        ClientProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $roleRequest->company_name,
                'phone'        => $roleRequest->phone,
            ]
        );

        // Update request
        $roleRequest->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'admin_note'  => $request->admin_note,
        ]);

        // Notify user
        app(NotificationService::class)->send(
            $user->id,
            'role_approved',
            'Ваша заявка на роль Заказчика одобрена! Теперь вы можете создавать заказы.',
            []
        );

        return back()->with('success', "Роль пользователя {$user->name} изменена на Заказчика.");
    }

    public function reject(Request $request, RoleRequest $roleRequest)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $roleRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'admin_note'  => $request->admin_note,
        ]);

        app(NotificationService::class)->send(
            $roleRequest->user_id,
            'role_rejected',
            'Ваша заявка на роль Заказчика отклонена.' .
            ($request->admin_note ? ' Причина: ' . $request->admin_note : ''),
            []
        );

        return back()->with('success', 'Заявка отклонена.');
    }
}