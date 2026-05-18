<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="keywords"
      content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Matrix lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Matrix admin lite design, Matrix admin lite dashboard bootstrap 5 dashboard template"
    />
    <meta
      name="description"
      content="Matrix Admin Lite Free Version is powerful and clean admin dashboard template, inpired from Bootstrap Framework"
    />
    <meta name="robots" content="noindex,nofollow" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}" ></script>
    {{-- <script src="{{ asset('assets/libs/switch/js/switch.min.js') }}" defer></script> --}}
    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}" defer></script>
    {{-- <script src="{{ asset('assets/libs/custom/js/custom.js') }}" defer></script> --}}
    <script src="{{ asset('assets/libs/chart/apexcharts/apexcharts.min.js') }}" defer></script>
    <script src="{{ asset('assets/libs/chart/chart.js') }}" defer></script>
    <script src="{{ asset('assets/libs/datatable/datatables.min.js') }}" defer></script>
    <script src="{{ asset('js/loader.js') }}" defer></script>


<!-- Styles -->
 <!-- Fonts -->
 <link rel="dns-prefetch" href="//fonts.gstatic.com">
 <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/libs/bootstrap/dist/css/bootstrap.min.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/bootstrap-icons/bootstrap-icons.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/switch/css/switch.min.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/select2/dist/css/select2.min.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/custom/css/custom.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/custom/css/sass.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/custom/css/custom2.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/custom/css/layout.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/custom/css/responsive.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/chart/apexcharts/apexcharts.css')}}"  >
<link rel="stylesheet" href="{{asset('assets/libs/datatable/datatables.min.css')}}"  >
<link rel="stylesheet" href="{{asset('css/loader.css')}}"  >







    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
</head>
<body>

        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div id="loading"></div>

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
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
                    @include('layouts/language_switcher')
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
        </nav>


        <div class="main-wrapper">
            @yield('content')

        </body>
        </html>

</body>
</html>
<script>

//     function changeLanguage(lang){
// alert(lang);
//         window.location='{{url("change-language")}}/'+lang;
//     }
    </script>
