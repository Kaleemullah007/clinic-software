<?php

namespace App\Http\Middleware;

use App\Models\DeviceApproval;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckDeviceApproval
{
    public function handle(Request $request, Closure $next)
    {
        // Not logged in → skip
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Superadmin always gets through
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Bypass when superadmin is impersonating another user
        if (session('impersonating_id')) {
            return $next($request);
        }

        // Check if feature is enabled (cached 5 min)
        $enabled = Cache::remember('device_approval_enabled', 300, fn() =>
            Setting::where('key_name', 'device_approval_enabled')->value('key_value') ?? '0'
        );

        if (! $enabled || $enabled === '0') {
            return $next($request);
        }

        // Feature is ON — verify browser token
        $cookieToken = $request->cookie('device_token');

        if ($cookieToken) {
            $device = DeviceApproval::where('token', $cookieToken)
                ->where('user_id', $user->id)
                ->first();

            if ($device && $device->status === 'approved') {
                return $next($request);
            }

            if ($device && $device->status === 'pending') {
                Auth::logout();
                $request->session()->put('device_pending_code', $device->code);
                return redirect()->route('device.pending');
            }

            if ($device && $device->status === 'rejected') {
                Auth::logout();
                $request->session()->put('device_pending_code', $device->code);
                return redirect()->route('device.pending');
            }
        }

        // No valid token — force re-login
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('login')
            ->with('error', 'Device approval required. Please log in again.');
    }
}
