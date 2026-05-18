<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <!--@vite(['resources/sass/app.scss', 'resources/js/app.js'])-->
<!--    <link rel="stylesheet" href="/public/build/assets/app.add836d3.css">-->
<!--<link rel="stylesheet" href="/public/build/assets/app.525f5899.css">-->
<!--<script src="/public/build/assets/app.ec4f2504.js"></script>-->

<link rel="stylesheet" href="{{ asset('build/assets/app.525f5899.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/app.add836d3.css')}}">
<script src="{{ asset('build/assets/app.ec4f2504.js')}}"></script>

</head>
<style>
    .bg-image {
        background-image: url('/assets/images/log-in.jpg') !important;
        background-size: cover;
        background-position: center;
    }
    .bg-auth {
        background-image: url('/assets/images/log-in.jpg');
        height: 100vh;
        background-size: cover;
        background-position: center;
    }
    .log-in {
        background-size: cover;
        min-height: 92.75vh;
    }
   
    .sign-in-css {
        /* border-radius: 10px; */
        /* width: 360px; */
        /* align-items: center; */
	    /* margin-top: 40px;
	    margin-bottom: auto; */
    }
    .log-in-logo {
        /* height: 100px; */
        /* width: 140px; */
        /* border-radius: 5px; */
    }
    .anchor-css,
    .anchor-css:hover {
        color: rgb(29, 29, 29) ;
    }
    .auth-content {
        
    }
</style>
<body>
    <div id="app" class="bg-auth">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav> --}}

        {{-- <main class="py-4"> --}}
            @yield('content')
        {{-- </main> --}}
    </div>
</body>
</html>
