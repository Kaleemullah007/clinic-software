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

    {{-- switch  --}}
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{--
<link rel="stylesheet" href="/build/assets/app.bf9eaf6a.css">
<link rel="stylesheet" href="/build/assets/app.525f5899.css">
<script src="public\build\assets\app.cb2bf7e6.js"></script> --}}

</head>

<body>

    <div class="">

        <!-- Navbar start -->
        <div class="bg-white fixed-top">
            <header class="header p-0 d-flex justify-content-between w-100 align-items-center navbar navbar-expand-sm expand-header">
                <div class="align-items-center d-flex ">
                    <div class="logo p-1 fs-3 fw-bold d-flex justify-content-center align-items-center">
                        {{-- <img src="/assets/images/img3.png" class="logo-image" alt="logo"> --}}
                        <span class="logo-text text-light"> RK Tech</span>
                    </div>
                    <a href="#" class="sidebarCollapse border border-secondary rounded ms-3" id="toggleSidebar" data-placement="bottom">
                        <i class="bi bi-list text-dark fs-4 px-1"></i>
                    </a>
                    <i class="bi bi-arrows-fullscreen fs-6 py-1 px-2 rounded ms-3 border border-secondary d-none d-sm-inline-block" onClick="toggleFullScreen()"></i>
                </div>
                <ul class="d-flex ms-auto list-unstyled pt-1 my-0">
                    <!-- Profile section -->
                    <li class="nav-item dropdown user-profile-dropdown d-flex justify-content-center align-items-center me-5">
                        <a href="#" class="fs-4" id="Notify" data-bs-toggle="dropdown">
                            <i class="bi text-theme bi-person-circle"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-css p-0 rounded">
                            <div class="user-profile-section">
                                <div class="bg-theme text-center pt-2">
                                    {{-- /assets/images/user3.png --}}
                                    <img src="{{ asset('images/avatar/' . auth()->user()->avatar) }}" alt="" class="img-fluid w-25 h-25 rounded-pill">
                                    <div class="media-body py-2 text-theme  ">
                                        <h4 class="fw-bold">{{ auth()->user()->name }}</h4>
                                        <span class="fs-5">{{ auth()->user()->role }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dp-main-menu">
                                <a href="/updateprofile/{{ auth()->id() }}/edit" class="dropdown-item p-3 d-flex"><span><i class="bi me-2 bi-person-fill"></i></span>Profile</a>
                                <a class="dropdown-item p-3 d-flex" href="#" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                    <span><i class="bi me-2 bi-box-arrow-left"></i></span>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </header>
        </div>
        <!-- Navbar End -->

        <!-- Left Sidebar  -->
        <div class="left-menu bg-white">
            <div class="menubar-content">
                <nav class="animated bounceInDown">
                    <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column list-unstyled p-0 m-0" id="sidebar">
                        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}"><i class="bi fs-5 bi-graph-up-arrow"></i><span class="hide-menu">Dashboard</span></a>
                        </li>

                        {{-- catagories Routes --}}
                        <!-- <li class="{{ request()->routeIs('category.*') ? 'active' : '' }} collapsed"
                            data-bs-toggle="collapse" data-bs-target="#category" aria-expanded="true">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>Categories</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>
                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('category.*') ? 'show' : '' }}"
                            id="category">
                            <li class="{{ request()->route()->getName() == 'category.create'? 'active': '' }}">
                                <a href="{{ route('category.create') }}"><i class="bi fs-5 bi-plus-circle"></i>
                                    <span class="hide-menu">Create Category</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'category.index'? 'active': '' }}">
                                <a href="{{ route('category.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Categories</span></a>
                            </li>
                        </ul> -->

                        {{-- pages Routes --}}
                        <!-- <li class="{{ request()->routeIs('pages.*') ? 'active' : ' collapsed' }} "
                            data-bs-toggle="collapse" data-bs-target="#pages"
                            aria-expanded="{{ request()->is('pages.*') ? true : false }} ">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>Pages</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>


                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('pages.*') ? 'show' : '' }}"
                            id="pages">
                            <li class="{{ request()->route()->getName() == 'pages.create'? 'active': '' }}">
                                <a href="{{ route('pages.create') }}"><i class="bi fs-5 bi-plus-circle"></i>
                                    <span class="hide-menu">Create Pages</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'pages.index'? 'active': '' }}">
                                <a href="{{ route('pages.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Pages</span></a>
                            </li>
                        </ul> -->

                        {{-- users Routes --}}
                        <li class="{{ request()->routeIs('users.*') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#users" aria-expanded="true">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>User Management</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>
                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('users.*') ? 'show' : '' }}" id="users">
                            <li class="{{ request()->route()->getName() == 'users.index'? 'active': '' }}">
                                <a href="{{ route('users.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Users</span></a>
                            </li>

                            <li class="{{ request()->route()->getName() == 'role.index'? 'active': '' }}">
                                <a href="{{ route('role.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Roles</span></a>
                            </li>

                            <li class="{{ request()->route()->getName() == 'permission.index'? 'active': '' }}">
                                <a href="{{ route('permission.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Permissions</span></a>
                            </li>

                            <li class="{{ request()->route()->getName() == 'module.index'? 'active': '' }}">
                                <a href="{{ route('module.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Modules</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'category.index'? 'active': '' }}">
                                <a href="{{ route('category.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Categories</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'pages.index'? 'active': '' }}">
                                <a href="{{ route('pages.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Pages</span></a>
                            </li>

                            {{-- business-hours Routes --}}
                            <li class="{{ request()->routeIs('businesshour.*') ? 'active' : '' }} collapsed">
                                <a href="{{ route('businesshour.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Business Hours</span></a>
                            </li>

                            {{-- email Routes --}}
                            <li class="{{ request()->routeIs('email.*') ? 'active' : '' }} collapsed">
                                <a href="{{ route('email.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Email
                                        Templates</span></a>
                            </li>
                            {{-- media Routes --}}
                            <li class="{{ request()->routeIs('media.*') ? 'active' : '' }} collapsed">
                                <a href="{{ route('media.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Media</span></a>
                            </li>
                        </ul>

                        {{-- Appointments Routes --}}
                        <li class="{{ request()->routeIs('appointments.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('appointments.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Appointments</span></a>
                        </li>

                        <!-- {{-- Prescription Routes --}}
                        <li class="{{ request()->routeIs('prescription.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('prescription.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Prescriptions</span></a>
                        </li> -->

                        <!-- {{-- media Routes --}}
                        <li class="{{ request()->routeIs('media.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('media.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Media</span></a>
                        </li> -->

                        {{-- blogs Routes --}}
                        <li class="{{ request()->routeIs('blogger.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('blogger.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Blogs</span></a>
                        </li>
                        <!-- {{-- blogs Routes --}}
                        <li class="{{ request()->routeIs('blogger.*') ? 'active' : ' collapsed' }} " data-bs-toggle="collapse" data-bs-target="#blogger" aria-expanded="{{ request()->is('blogger.*') ? true : false }} ">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>Blog</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>
                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('blogger.*') ? 'show' : '' }}" id="blogger">
                            <li class="{{ request()->route()->getName() == 'blogger.create'? 'active': '' }}">
                                <a href="{{ route('blogger.create') }}"><i class="bi fs-5 bi-plus-circle"></i>
                                    <span class="hide-menu">Create Blog</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'blogger.index'? 'active': '' }}">
                                <a href="{{ route('blogger.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Blogs</span></a>
                            </li>
                        </ul> -->

                        <!-- {{-- business-hours Routes --}}
                        <li class="{{ request()->routeIs('businesshour.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('businesshour.index') }}"><i
                                    class="bi fs-5 bi-list-ul"></i><span>Business Hours</span></a>
                        </li>

                        {{-- email Routes --}}
                        <li class="{{ request()->routeIs('email.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('email.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Email
                                    Templates</span></a>
                        </li> -->
                        {{-- contacts Routes --}}
                        <li class="{{ request()->routeIs('contacts.*') ? 'active' : '' }} collapsed">
                            <a href="{{ route('contacts.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Contacts</span></a>
                        </li>

                        <!-- settings Routes -->
                        <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="true">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>Settings</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>
                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}" id="settings">

                            <li class="{{ request()->route()->getName() == 'settings.index'? 'active': '' }}">
                                <a href="{{ route('settings.index') }}"><i class="bi fs-5 bi-bounding-box-circles"></i>
                                    <span class="hide-menu">Settings</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'placeholder.index'? 'active': '' }}">
                                <a href="{{ route('placeholder.index') }}"><i class="bi fs-5 bi-plus-circle"></i>
                                    <span class="hide-menu">Placeholder</span></a>
                            </li>
                            <li class="{{ request()->route()->getName() == 'clinic.index'? 'active': '' }}">
                                <a href="{{ route('clinic.index') }}"><i class="bi fs-5 bi-plus-circle"></i>
                                    <span class="hide-menu">Clinic</span></a>
                            </li>
                        </ul>
                        <!-- reports Routes -->
                        <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#reports" aria-expanded="true">
                            <a href="#"><i class="bi fs-5 bi-diagram-3"></i><span>Reports</span>
                                <i class="bi bi-caret-down-fill right"></i></a>
                        </li>
                        <ul class="submenu list-unstyled collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reports">
                            {{-- Prescription Routes --}}
                            <li class="{{ request()->routeIs('prescription.*') ? 'active' : '' }} collapsed">
                                <a href="{{ route('prescription.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Prescriptions</span></a>
                            </li>
                        </ul>

                        <!-- {{-- settings Routes --}}
                        {{-- <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }} collapsed">
                        <a href="{{ route('settings.index') }}"><i class="bi fs-5 bi-list-ul"></i><span>Settings</span></a>
                        </li> --}} -->


                    </ul>
                </nav>
            </div>
        </div>
        <!-- End Left Sidebar  -->

        <div class="content-wrapper">
            <div class="min-height-css">


                @yield('content')


            </div>
        </div>
    </div>

</body>

</html>

{{-- https://gitbrent.github.io/bootstrap-switch-button/ --}}
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js">

</script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}" ></script>
<script src="{{ asset('assets/libs/datatable/datatables.min.js') }}" defer></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
@yield('script')
<script>
    //  --------sidebar active transition js ---------
    $(document).on('click', '#sidebar li', function() {
        $(this).addClass('active').siblings().removeClass('active')
    });

    //  --------sidebar collapse toggle js ---------
    $(document).ready(function() {
        $("#toggleSidebar").click(function() {
            $(".left-menu").toggleClass("hide");
            $(".content-wrapper").toggleClass("hide");
        });
    });
</script>
<script src="/custom.js"  ></script>
