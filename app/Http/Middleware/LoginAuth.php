<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
{
    if (Auth::guard('web')->check()) {
        $user = Auth::user();

        if (!$user->role) {
            return redirect()->route('getLogin')->with('error', 'You have to be an admin user to access this page');
        }

        // Example: restrict 'staffs' from accessing 'users' or 'office'
        if ($user->role==('staffs')) {
            if ($request->is('users') || $request->is('office') || $request->is('users/*') || $request->is('office/*')) {
                return redirect()->route('folders')->with('error1', 'You do not have permission to access this page');
            }
        }

    } else {
        return redirect()->route('getLogin')->with('error', 'You have to Sign In first to access this page');
    }

    // Cache prevention
    $response = $next($request);
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    return $response;
}

}