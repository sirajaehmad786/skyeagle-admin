<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Admin panel: active status check only.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (! AdminAccess::userMayAccessPanel($user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = __('auth.account_inactive');

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
