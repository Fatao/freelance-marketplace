<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRequestController extends Controller
{
    public function create()
    {
        // Check if already has pending request
        $existing = RoleRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        return view('role_requests.create', compact('existing'));
    }

    public function store(Request $request)
    {
        // Check already client
        if (Auth::user()->isClient()) {
            return back()->with('error', 'Вы уже являетесь заказчиком.');
        }

        // Check already pending
        $existing = RoleRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Ваша заявка уже на рассмотрении.');
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'reason'       => 'nullable|string|max:1000',
        ]);

        RoleRequest::create(array_merge($data, [
            'user_id'        => Auth::id(),
            'requested_role' => 'client',
            'status'         => 'pending',
        ]));

        // Notify all admins
        \App\Models\User::where('role', 'admin')->each(function ($admin) {
            app(NotificationService::class)->send(
                $admin->id,
                'role_request',
                'Новая заявка на роль Заказчика от ' . Auth::user()->name,
                ['user_id' => Auth::id()]
            );
        });

        return redirect()->route('role-request.create')
            ->with('success', 'Заявка отправлена. Администратор рассмотрит её в ближайшее время.');
    }
}