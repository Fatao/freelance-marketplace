<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_blocked) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Ваш аккаунт заблокирован.']);
        }
        return $next($request);
    }
}