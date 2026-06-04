<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceApproval;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /* ── Inactive account check — fires before credentials are verified ── */
    protected function validateLogin(HttpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->status) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ['Your account is inactive. Please contact the administrator.'],
            ]);
        }

        $request->validate([
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ]);
    }

    /* ── Device approval check — fires after credentials are verified ── */
    protected function authenticated(HttpRequest $request, $user)
    {
        // Check if feature is enabled (cached 5 min)
        $enabled = Cache::remember('device_approval_enabled', 300, fn() =>
            Setting::where('key_name', 'device_approval_enabled')->value('key_value') ?? '0'
        );

        // Feature off or superadmin → proceed normally
        if (! $enabled || $enabled === '0' || $user->isSuperAdmin()) {
            return null;
        }

        $cookieToken = $request->cookie('device_token');

        if ($cookieToken) {
            $device = DeviceApproval::where('token', $cookieToken)
                ->where('user_id', $user->id)
                ->first();

            // Approved device → login proceeds
            if ($device && $device->status === 'approved') {
                return null;
            }

            // Pending or rejected → block and show waiting page
            if ($device) {
                $this->guard()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $request->session()->put('device_pending_code', $device->code);
                return redirect()->route('device.pending');
            }
        }

        // New device — create a pending approval request
        $token = Str::random(64);
        $code  = 'DEV-' . strtoupper(Str::random(6));

        DeviceApproval::create([
            'code'       => $code,
            'user_id'    => $user->id,
            'token'      => $token,
            'browser'    => substr($request->userAgent() ?? '', 0, 255),
            'ip_address' => $request->ip(),
            'status'     => 'pending',
        ]);

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('device_pending_code', $code);

        return redirect()->route('device.pending')
            ->withCookie(cookie()->forever('device_token', $token));
    }

    public function logout(HttpRequest $request)
    {
        Cache::forget('user'.auth()->id());
        $this->guard()->logout();
        Auth::logout();
        Session::flush();
        Session::regenerate();

        return redirect('/login');
    }

}
