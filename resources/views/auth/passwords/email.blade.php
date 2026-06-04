@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<div class="auth-wrapper">

    {{-- ── LEFT: Brand ─────────────────────────────────────────── --}}
    <div class="auth-left">
        <div class="auth-brand">
            <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech Logo">
            <span class="auth-brand-name">RKTech</span>
        </div>
        <h1 class="auth-headline">Password<br><span>Recovery</span></h1>
        <p class="auth-subline">Enter your registered email address and we'll send you a secure link to reset your password.</p>
        <ul class="auth-features">
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm8 7L4 6h16l-8 5z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Check Your Inbox</h4>
                    <p>A reset link will be sent to your email</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Secure Reset</h4>
                    <p>Links expire after 60 minutes for safety</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="white" stroke-width="2" fill="none"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Account Recovery</h4>
                    <p>Regain access to your clinic account</p>
                </div>
            </li>
        </ul>
    </div>

    {{-- ── CENTER: Forgot Password Form ───────────────────────── --}}
    <div class="auth-center">
        <div class="auth-form-wrap">
            <div class="auth-form-logo">
                <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech">
            </div>
            <h2 class="auth-form-title">Forgot password?</h2>
            <p class="auth-form-subtitle">Enter your email to receive a reset link</p>

            @if (session('status'))
                <div class="auth-alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="auth-field">
                    <label for="email">Email Address</label>
                    <div class="auth-input-wrap">
                        <span class="fi">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                        </span>
                        <input id="email" type="email" name="email"
                            class="@error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required autocomplete="email" autofocus
                            placeholder="your@email.com">
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn-auth-main">Send Reset Link</button>
            </form>

            <div class="auth-divider mt-3">remembered it?</div>

            <a href="{{ route('login') }}" class="btn-auth-outline">Back to Sign In</a>
        </div>
    </div>

    {{-- ── RIGHT: Illustration ─────────────────────────────────── --}}
    <div class="auth-right">
        <div class="dc1"></div>
        <div class="dc2"></div>
        <div class="dc3"></div>
        <div class="auth-illus">
            <svg width="220" height="260" viewBox="0 0 220 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="110" cy="130" r="95" fill="white" opacity="0.5"/>
                <!-- Envelope -->
                <rect x="30" y="80" width="160" height="110" rx="10" fill="white" opacity="0.92"/>
                <path d="M30 90 L110 145 L190 90" stroke="#921b9b" stroke-width="2.5" fill="none"/>
                <path d="M30 175 L80 130" stroke="#e5e7eb" stroke-width="2" fill="none"/>
                <path d="M190 175 L140 130" stroke="#e5e7eb" stroke-width="2" fill="none"/>
                <!-- Lock icon inside envelope area -->
                <rect x="88" y="112" width="44" height="34" rx="6" fill="#f0e8ff"/>
                <path d="M98 112 Q98 100 110 100 Q122 100 122 112" stroke="#921b9b" stroke-width="3" fill="none"/>
                <circle cx="110" cy="127" r="5" fill="#921b9b"/>
                <rect x="108" y="126" width="4" height="8" rx="2" fill="#921b9b"/>
                <!-- Stars -->
                <circle cx="168" cy="72" r="4" fill="#f5a623" opacity="0.7"/>
                <circle cx="52" cy="68" r="3" fill="#921b9b" opacity="0.5"/>
                <circle cx="180" cy="55" r="2.5" fill="#d13729" opacity="0.6"/>
                <circle cx="40" cy="55" r="2" fill="#072e75" opacity="0.5"/>
            </svg>
            <div class="auth-stat-pill">
                <div class="num">100%</div>
                <div class="lbl">Secure Reset Process</div>
            </div>
        </div>
    </div>

</div>
@endsection
