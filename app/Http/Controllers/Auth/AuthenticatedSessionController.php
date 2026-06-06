<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Ваш аккаунт заблокирован. Причина: ' . ($user->block_reason ?? 'не указана')]);
        }

        return match($user->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'moderator' => redirect()->route('moderator.dashboard'),
            default     => redirect()->route('orders.index'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}