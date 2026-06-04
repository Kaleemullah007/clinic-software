@extends('layouts.auth')
@section('title', 'Set New Password')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
@csrf
<input type="hidden" name="token" value="{{ $token }}">

<div class="auth-wrapper">

    {{-- ── LEFT: Brand ─────────────────────────────────────────── --}}
    <div class="auth-left">
        <div class="auth-brand">
            <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech Logo">
            <span class="auth-brand-name">RKTech</span>
        </div>
        <h1 class="auth-headline">Set Your<br><span>New Password</span></h1>
        <p class="auth-subline">Choose a strong password to protect your clinic account. Make it unique and hard to guess.</p>
        <ul class="auth-features">
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Use 8+ Characters</h4>
                    <p>Mix letters, numbers and symbols</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>Avoid Reuse</h4>
                    <p>Don't reuse your previous password</p>
                </div>
            </li>
            <li class="auth-feature-item">
                <div class="auth-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                </div>
                <div class="auth-feature-text">
                    <h4>All Set</h4>
                    <p>You'll be signed in automatically</p>
                </div>
            </li>
        </ul>
    </div>

    {{-- ── CENTER: Reset Password Form ────────────────────────── --}}
    <div class="auth-center">
        <div class="auth-form-wrap">
            <div class="auth-form-logo">
                <img src="{{ asset('assets/images/rktech-logo.jpg') }}" alt="RKTech">
            </div>
            <h2 class="auth-form-title">New password</h2>
            <p class="auth-form-subtitle">Enter and confirm your new password below</p>

            {{-- Email --}}
            <div class="auth-field">
                <label for="email">Email Address</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                    </span>
                    <input id="email" type="email" name="email"
                        class="@error('email') is-invalid @enderror"
                        value="{{ $email ?? old('email') }}"
                        required autocomplete="email" autofocus
                        placeholder="your@email.com">
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            {{-- New Password --}}
            <div class="auth-field">
                <label for="password">New Password</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input id="password" type="password" name="password"
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
                <label for="password-confirm">Confirm New Password</label>
                <div class="auth-input-wrap">
                    <span class="fi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input id="password-confirm" type="password" name="password_confirmation"
                        class="@error('password_confirmation') is-invalid @enderror"
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

            <button type="submit" class="btn-auth-main">Reset Password</button>

            <div class="auth-divider mt-3">or</div>

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
                <!-- Shield -->
                <path d="M110 30 L170 55 L170 115 Q170 160 110 185 Q50 160 50 115 L50 55 Z" fill="white" opacity="0.9"/>
                <path d="M110 45 L155 65 L155 115 Q155 148 110 168 Q65 148 65 115 L65 65 Z" fill="#f0e8ff" opacity="0.7"/>
                <!-- Checkmark -->
                <path d="M82 112 L102 132 L138 96" stroke="#921b9b" stroke-width="7" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Lock at bottom -->
                <rect x="88" y="190" width="44" height="34" rx="8" fill="white" opacity="0.9"/>
                <path d="M98 190 Q98 178 110 178 Q122 178 122 190" stroke="#921b9b" stroke-width="3" fill="none"/>
                <circle cx="110" cy="205" r="5" fill="#921b9b"/>
                <rect x="108" y="204" width="4" height="8" rx="2" fill="#921b9b"/>
            </svg>
            <div class="auth-stat-pill">
                <div class="num">256-bit</div>
                <div class="lbl">Encrypted & Secure</div>
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
