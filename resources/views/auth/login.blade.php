@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<form method="POST" action="{{ route('login') }}">
@csrf

<div class="auth-wrapper">

    {{-- ── LEFT: Brand + Features ─────────────────────────────── --}}
    <div class="auth-left">
        <div class="auth-brand">
            <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech Logo">
            <span class="auth-brand-name">RKTech</span>
        </div>
        <h1 class="auth-headline">All-in-one Platform<br>for <span>Clinics</span></h1>
        <p class="auth-subline">Manage patients, appointments, inventory and deliver exceptional clinical care — all from one place.</p>
        <ul class="auth-features">
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Patient Management</h4>
                    <p>Organize records, prescriptions and progress</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Appointment Scheduling</h4>
                    <p>Manage time slots and bookings effortlessly</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 3H4v10c0 2.2 1.8 4 4 4h6c2.2 0 4-1.8 4-4v-3h2c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Inventory Control</h4>
                    <p>Track stock, purchases and product movements</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Reports & Analytics</h4>
                    <p>Revenue, performance and financial insights</p>
                </div>
            </li>
        </ul>
    </div>

    {{-- ── CENTER: Login Form ──────────────────────────────────── --}}
    <div class="auth-center">
        <div class="auth-form-wrap">
            <div class="auth-form-logo">
                <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech">
            </div>
            <h2 class="auth-form-title">Welcome back</h2>
            <p class="auth-form-subtitle">Sign in to continue to your account</p>

            {{-- Email --}}
            <div class="auth-field">
                <label for="email">Email Address</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                    </span>
                    <input type="email" id="email" name="email"
                        class="@error('email') is-invalid @enderror"
                        value="{{ old('email','') }}"
                        required autocomplete="email" autofocus
                        placeholder="your@email.com">
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="auth-field">
                <label for="password">Password</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input type="password" id="password" name="password"
                        class="@error('password') is-invalid @enderror"
                        required autocomplete="current-password"
                        placeholder="••••••••">
                    <button type="button" class="eye-btn" onclick="togglePwd('password',this)" tabindex="-1">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            {{-- Remember + Forgot --}}
            <div class="auth-meta">
                <label class="auth-remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-forgot">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-auth-main">Sign In</button>

            <div class="auth-divider mt-3">or</div>

            <a href="{{ langUrl('register') }}" class="btn-auth-outline">Create an account</a>
        </div>
    </div>

    {{-- ── RIGHT: Medical Illustration ────────────────────────── --}}
    <div class="auth-right">
        <div class="dc1"></div>
        <div class="dc2"></div>
        <div class="dc3"></div>
        <div class="auth-illus">
            <svg width="240" height="300" viewBox="0 0 240 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="120" cy="150" r="105" fill="white" opacity="0.55"/>
                <ellipse cx="120" cy="270" rx="52" ry="22" fill="#e8d5f5" opacity="0.7"/>
                <rect x="78" y="178" width="84" height="90" rx="12" fill="white" opacity="0.95"/>
                <path d="M78 178 L104 204 L120 188 L136 204 L162 178" stroke="#d1d5db" stroke-width="1.5" fill="none"/>
                <rect x="111" y="158" width="18" height="24" rx="5" fill="#f5d0b5"/>
                <ellipse cx="120" cy="138" rx="30" ry="36" fill="#f5d0b5"/>
                <ellipse cx="120" cy="108" rx="30" ry="17" fill="#4a3728"/>
                <rect x="90" y="103" width="60" height="18" rx="8" fill="#4a3728"/>
                <circle cx="110" cy="138" r="3.5" fill="#2d3748"/>
                <circle cx="130" cy="138" r="3.5" fill="#2d3748"/>
                <circle cx="111" cy="137" r="1.2" fill="white"/>
                <circle cx="131" cy="137" r="1.2" fill="white"/>
                <path d="M118 145 Q120 149 122 145" stroke="#c0845c" stroke-width="1.2" fill="none"/>
                <path d="M113 152 Q120 159 127 152" stroke="#c0845c" stroke-width="2" fill="none" stroke-linecap="round"/>
                <path d="M98 188 Q90 208 93 223 Q97 236 108 236 Q119 236 119 226" stroke="#921b9b" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                <path d="M142 188 Q150 208 147 223 Q143 236 132 236 Q121 236 119 226" stroke="#921b9b" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                <circle cx="119" cy="226" r="7" fill="#921b9b"/>
                <circle cx="119" cy="226" r="3.5" fill="white"/>
                <circle cx="95" cy="186" r="5.5" fill="#6d1277"/>
                <circle cx="145" cy="186" r="5.5" fill="#6d1277"/>
                <rect x="136" y="202" width="34" height="44" rx="5" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="1.5"/>
                <rect x="142" y="197" width="22" height="8" rx="4" fill="#d1d5db"/>
                <line x1="142" y1="216" x2="164" y2="216" stroke="#d1d5db" stroke-width="2"/>
                <line x1="142" y1="224" x2="164" y2="224" stroke="#d1d5db" stroke-width="2"/>
                <line x1="142" y1="232" x2="158" y2="232" stroke="#d1d5db" stroke-width="2"/>
                <rect x="112" y="212" width="16" height="5" rx="2" fill="#921b9b" opacity="0.65"/>
                <rect x="117" y="207" width="6" height="15" rx="2" fill="#921b9b" opacity="0.65"/>
            </svg>
            <div class="auth-stat-pill">
                <div class="num">500+</div>
                <div class="lbl">Patients Managed Daily</div>
            </div>
        </div>
    </div>

</div>
</form>

<script>
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.innerHTML = show
        ? '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
}
</script>
@endsection
