<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->isClient()) {
            $profile = $user->clientProfile;
            if (!$profile || empty($profile->company_name) || empty($profile->phone)) {
                return redirect()->route('profile.client.edit')
                    ->with('error', 'Заполните профиль компании для размещения заказов.');
            }
        }

        return $next($request);
    }
}