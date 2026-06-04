@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<form method="POST" action="{{ route('register') }}">
@csrf

<div class="auth-wrapper">

    {{-- ── LEFT: Brand ─────────────────────────────────────────── --}}
    <div class="auth-left">
        <div class="auth-brand">
            <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech Logo">
            <span class="auth-brand-name">RKTech</span>
        </div>
        <h1 class="auth-headline">Join the Platform<br>Built for <span>Clinics</span></h1>
        <p class="auth-subline">Start managing your clinic smarter — appointments, patients, inventory and reports all in one place.</p>
        <ul class="auth-features">
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Secure & Reliable</h4>
                    <p>Your data is safe and protected</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Multi-Role Access</h4>
                    <p>Doctors, staff and admin roles</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Real-time Insights</h4>
                    <p>Revenue, expenses and performance</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>WhatsApp Integration</h4>
                    <p>Send receipts and campaigns instantly</p>
                </div>
            </li>
        </ul>
    </div>

    {{-- ── CENTER: Register Form ───────────────────────────────── --}}
    <div class="auth-center">
        <div class="auth-form-wrap">
            <div class="auth-form-logo">
                <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech">
            </div>
            <h2 class="auth-form-title">Create account</h2>
            <p class="auth-form-subtitle">Fill in your details to get started</p>

            {{-- Name --}}
            <div class="auth-field">
                <label for="username">Full Name</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input id="username" type="text"
                        class="@error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}"
                        required autocomplete="username" autofocus
                        placeholder="Imran Khan">
                    @error('name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="auth-field">
                <label for="email">Email Address</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                    </span>
                    <input type="email" id="email" name="email"
                        class="@error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required autocomplete="email"
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
                        required autocomplete="new-password"
                        placeholder="••••••••">
                    <button type="button" class="eye-btn" onclick="togglePwd('password',this)" tabindex="-1">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="auth-field">
                <label for="password-confirm">Confirm Password</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input id="password-confirm" type="password"
                        class="@error('password_confirmation') is-invalid @enderror"
                        name="password_confirmation"
                        required autocomplete="new-password"
                        placeholder="••••••••">
                    <button type="button" class="eye-btn" onclick="togglePwd('password-confirm',this)" tabindex="-1">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    @error('password_confirmation')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-auth-main">Register Now</button>

            <div class="auth-divider mt-3">already have an account?</div>

            <a href="{{ route('login') }}" class="btn-auth-outline">Sign In</a>
        </div>
    </div>

    {{-- ── RIGHT: Illustration ─────────────────────────────────── --}}
    <div class="auth-right">
        <div class="dc1"></div>
        <div class="dc2"></div>
        <div class="dc3"></div>
        <div class="auth-illus">
            <svg width="220" height="280" viewBox="0 0 220 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="110" cy="140" r="95" fill="white" opacity="0.5"/>
                <!-- Clinic building -->
                <rect x="45" y="120" width="130" height="120" rx="6" fill="white" opacity="0.9"/>
                <rect x="45" y="108" width="130" height="20" rx="4" fill="#921b9b" opacity="0.85"/>
                <!-- Medical cross on building -->
                <rect x="100" y="80" width="20" height="6" rx="3" fill="#921b9b"/>
                <rect x="106" y="74" width="8" height="18" rx="3" fill="#921b9b"/>
                <!-- Windows -->
                <rect x="58" y="138" width="28" height="22" rx="3" fill="#dbeafe"/>
                <rect x="96" y="138" width="28" height="22" rx="3" fill="#dbeafe"/>
                <rect x="134" y="138" width="28" height="22" rx="3" fill="#dbeafe"/>
                <rect x="58" y="172" width="28" height="22" rx="3" fill="#dbeafe"/>
                <rect x="134" y="172" width="28" height="22" rx="3" fill="#dbeafe"/>
                <!-- Door -->
                <rect x="97" y="178" width="26" height="36" rx="4" fill="#e8d5f5"/>
                <circle cx="119" cy="197" r="2.5" fill="#921b9b"/>
                <!-- Path -->
                <rect x="100" y="214" width="20" height="26" fill="#f3f0ff"/>
                <!-- Ground -->
                <rect x="30" y="238" width="160" height="6" rx="3" fill="#e8d5f5" opacity="0.8"/>
                <!-- Staff figure -->
                <circle cx="165" cy="148" r="10" fill="#f5d0b5"/>
                <ellipse cx="165" cy="158" rx="8" ry="4" fill="#4a3728"/>
                <rect x="157" y="161" width="16" height="22" rx="4" fill="white" opacity="0.9"/>
                <circle cx="165" cy="168" r="4" fill="#921b9b" opacity="0.6"/>
            </svg>
            <div class="auth-stat-pill">
                <div class="num">50+</div>
                <div class="lbl">Clinics Powered by RKTech</div>
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
