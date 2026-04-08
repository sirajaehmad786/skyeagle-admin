<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(Auth::check()){
            $user = Auth::user();

            if($user->status != config('constant.user_status.Active')){
                Auth::logout();

                return redirect()->route('login')->with('error', 'Your account is Inactivated. Please contact admin');
            }
        }
        return $next($request);
    }
}
