<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemLockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip for admin routes
        if ($request->is('system/*') || $request->is('system/auth/*')) {
            return $next($request);
        }

        // Skip for login, logout
        if ($request->is('login') || $request->is('logout') || $request->is('/')) {
            return $next($request);
        }

        // Check if system is locked
        if (SystemSetting::get('system_locked', false)) {
            $lockReason = SystemSetting::get('lock_reason', 'System is currently locked.');
            return response()->view('errors.system-locked', [
                'message' => $lockReason,
                'type' => 'locked'
            ], 503);
        }

        // Check subscription status
        if (SystemSetting::get('subscription_status') === 'inactive') {
            $dueDate = SystemSetting::get('subscription_due_date', 'N/A');
            return response()->view('errors.system-locked', [
                'message' => "System access suspended due to unpaid subscription. Payment was due on {$dueDate}.",
                'type' => 'payment'
            ], 503);
        }

        // Check date-range lock
        $lockStart = SystemSetting::get('lock_start_date');
        $lockEnd = SystemSetting::get('lock_end_date');
        
        if ($lockStart && $lockEnd) {
            $now = now();
            $start = \Carbon\Carbon::parse($lockStart);
            $end = \Carbon\Carbon::parse($lockEnd);
            
            if ($now->between($start, $end)) {
                $lockReason = SystemSetting::get('lock_reason', 'System is locked for maintenance.');
                return response()->view('errors.system-locked', [
                    'message' => $lockReason . " Locked from {$lockStart} to {$lockEnd}.",
                    'type' => 'scheduled'
                ], 503);
            }
        }

        // Check maintenance mode
        if (SystemSetting::get('maintenance_mode', false)) {
            $maintenanceMessage = SystemSetting::get('maintenance_message', 'System is under maintenance.');
            return response()->view('errors.system-locked', [
                'message' => $maintenanceMessage,
                'type' => 'maintenance'
            ], 503);
        }

        return $next($request);
    }
}