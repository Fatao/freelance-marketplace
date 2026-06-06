<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['freelancerProfile', 'clientProfile'])
            ->orderByDesc('created_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('blocked')) {
            $query->where('is_blocked', (bool)$request->blocked);
        }

        $users = $query->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['freelancerProfile.skills', 'clientProfile', 'orders', 'applications']);
        return view('admin.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:freelancer,client,moderator,admin']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'Роль обновлена.');
    }

    public function block(Request $request, User $user)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);
        $user->update(['is_blocked' => true, 'block_reason' => $request->reason]);
        return back()->with('success', 'Пользователь заблокирован.');
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false, 'block_reason' => null]);
        return back()->with('success', 'Пользователь разблокирован.');
    }
}