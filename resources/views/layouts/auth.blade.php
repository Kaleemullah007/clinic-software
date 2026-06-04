<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RKTech — @yield('title', 'Sign In')</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/assets/app.525f5899.css')}}">
    <link rel="stylesheet" href="{{ asset('build/assets/app.add836d3.css')}}">
    <script src="{{ asset('build/assets/app.ec4f2504.js')}}"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
            background: #fff;
            overflow: hidden;
        }

        /* ── Wrapper ─────────────────────────────────────────────── */
        .auth-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Left Panel ──────────────────────────────────────────── */
        .auth-left {
            width: 38%;
            background: linear-gradient(160deg, #072e75 0%, #3d1a6e 50%, #921b9b 100%);
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 300px; height: 300px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -90px; left: -90px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
        }
        .auth-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 44px;
        }
        .auth-brand img {
            height: 44px;
            border-radius: 8px;
            object-fit: contain;
        }
        .auth-brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .auth-headline {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.35;
            margin: 0 0 14px;
        }
        .auth-headline span { color: #f5a623; }
        .auth-subline {
            font-size: 14px;
            opacity: .78;
            margin: 0 0 38px;
            line-height: 1.65;
        }
        .auth-features {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .auth-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .auth-feature-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,.13);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .auth-feature-icon svg { width: 20px; height: 20px; fill: #fff; }
        .auth-feature-text h4 {
            font-size: 13.5px;
            font-weight: 700;
            margin: 0 0 2px;
        }
        .auth-feature-text p {
            font-size: 12px;
            opacity: .68;
            margin: 0;
        }

        /* ── Center Panel ────────────────────────────────────────── */
        .auth-center {
            width: 34%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 44px;
            flex-shrink: 0;
        }
        .auth-form-wrap {
            width: 100%;
            max-width: 360px;
        }
        .auth-form-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-form-logo img {
            height: 48px;
            border-radius: 8px;
            object-fit: contain;
        }
        .auth-form-title {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 5px;
        }
        .auth-form-subtitle {
            font-size: 13.5px;
            color: #6c757d;
            margin: 0 0 26px;
        }

        /* Inputs */
        .auth-field { margin-bottom: 16px; }
        .auth-field label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }
        .auth-input-wrap { position: relative; }
        .auth-input-wrap .fi {
            position: absolute;
            left: 11px; top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: flex;
        }
        .auth-input-wrap input {
            width: 100%;
            padding: 10px 36px 10px 36px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1a1a2e;
            background: #f9fafb;
            outline: none;
            transition: border-color .2s, background .2s;
            font-family: 'Nunito', sans-serif;
        }
        .auth-input-wrap input:focus {
            border-color: #921b9b;
            background: #fff;
        }
        .auth-input-wrap input.is-invalid { border-color: #dc3545; }
        .auth-input-wrap .eye-btn {
            position: absolute;
            right: 10px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            background: none;
            border: none;
            padding: 2px;
            display: flex;
            align-items: center;
        }
        .invalid-feedback {
            font-size: 11.5px;
            color: #dc3545;
            display: block;
            margin-top: 4px;
        }

        /* Remember / forgot row */
        .auth-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .auth-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
        }
        .auth-remember input[type="checkbox"] { accent-color: #921b9b; cursor: pointer; }
        .auth-forgot {
            font-size: 13px;
            font-weight: 700;
            color: #921b9b;
            text-decoration: none;
        }
        .auth-forgot:hover { color: #d13729; }

        /* Buttons */
        .btn-auth-main {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #921b9b 5%, #d13729);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .4px;
            font-family: 'Nunito', sans-serif;
            transition: opacity .2s;
            text-align: center;
            text-decoration: none;
        }
        .btn-auth-main:hover { opacity: .9; color: #fff; }
        .btn-auth-outline {
            display: block;
            width: 100%;
            padding: 11px;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-family: 'Nunito', sans-serif;
            transition: border-color .2s, color .2s;
        }
        .btn-auth-outline:hover { border-color: #921b9b; color: #921b9b; }

        /* Divider */
        .auth-divider {
            text-align: center;
            margin: 16px 0;
            position: relative;
            font-size: 12.5px;
            color: #9ca3af;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 43%;
            height: 1px;
            background: #e5e7eb;
        }
        .auth-divider::before { left: 0; }
        .auth-divider::after { right: 0; }

        /* Alert */
        .auth-alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* ── Right Panel ─────────────────────────────────────────── */
        .auth-right {
            flex: 1;
            background: linear-gradient(160deg, #f0e8ff 0%, #ffddeb 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-right .dc1 {
            position: absolute;
            top: -70px; right: -70px;
            width: 240px; height: 240px;
            background: rgba(146,27,155,.09);
            border-radius: 50%;
        }
        .auth-right .dc2 {
            position: absolute;
            bottom: -90px; left: -90px;
            width: 300px; height: 300px;
            background: rgba(209,55,41,.07);
            border-radius: 50%;
        }
        .auth-right .dc3 {
            position: absolute;
            top: 42%; left: -50px;
            width: 130px; height: 130px;
            background: rgba(7,46,117,.06);
            border-radius: 50%;
        }
        .auth-illus {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            padding: 20px;
        }
        .auth-stat-pill {
            background: #fff;
            border-radius: 14px;
            padding: 13px 20px;
            box-shadow: 0 6px 24px rgba(0,0,0,.09);
            text-align: center;
        }
        .auth-stat-pill .num {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(to right, #921b9b, #d13729);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-stat-pill .lbl {
            font-size: 11.5px;
            font-weight: 600;
            color: #6c757d;
            margin-top: 2px;
        }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .auth-right { display: none; }
            .auth-center { width: 50%; }
            .auth-left   { width: 50%; }
        }
        @media (max-width: 768px) {
            .auth-left   { display: none; }
            .auth-center { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
