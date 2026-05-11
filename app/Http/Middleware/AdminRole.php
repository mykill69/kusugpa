<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('admin.getLogin')
                ->with('admin_error', 'Please sign in to access the admin panel.');
        }

        $user = Auth::user();
        
        if ($user->role !== 'Administrator' && $user->role !== 'super_admin') {
            Auth::logout();
            return redirect()->route('admin.getLogin')
                ->with('admin_error', 'Unauthorized access attempt logged.');
        }

        return $next($request);
    }
}