<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Clinic') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('build/assets/app.525f5899.css')}}">
    <link rel="stylesheet" href="{{ asset('build/assets/app.add836d3.css')}}">
    <script src="{{ asset('build/assets/app.ec4f2504.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css')}}">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
</head>
<body>
<div class="">

    <!-- Navbar -->
    <div class="bg-white fixed-top">
        <header class="header p-0 d-flex justify-content-between w-100 align-items-center navbar navbar-expand-sm expand-header">
            <div class="align-items-center d-flex">
                <div class="logo p-1 fs-3 fw-bold d-flex justify-content-center align-items-center">
                    <span class="logo-text text-light"> RK Tech</span>
                </div>
                <a href="#" class="sidebarCollapse border border-secondary rounded ms-3 d-block d-sm-none" id="toggleSidebar__" data-placement="bottom">
                    <i class="bi bi-list text-dark fs-4 px-1"></i>
                </a>
                <a href="#" class="sidebarCollapse border border-secondary rounded ms-3 d-none d-sm-block" id="toggleSidebar" data-placement="bottom">
                    <i class="bi bi-list text-dark fs-4 px-1"></i>
                </a>
                <i class="bi bi-arrows-fullscreen fs-6 py-1 px-2 rounded ms-3 border border-secondary d-none d-sm-block" onClick="toggleFullScreen()"></i>
            </div>
            <ul class="d-flex ms-auto list-unstyled pt-1 my-0">
                <li class="nav-item dropdown user-profile-dropdown d-flex justify-content-center align-items-center me-5">
                    <a href="#" class="fs-4" id="Notify" data-bs-toggle="dropdown">
                        <i class="bi text-theme bi-person-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-css p-0 rounded">
                        <div class="user-profile-section">
                            <div class="bg-theme text-center pt-2">
                                <img src="{{ asset('images/avatar/' . auth()->user()->avatar) }}" alt="" class="img-fluid w-25 h-25 rounded-pill">
                                <div class="media-body py-2 text-theme">
                                    <h4 class="fw-bold">{{ auth()->user()->name }}</h4>
                                    <span class="fs-5">{{ auth()->user()->role }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dp-main-menu">
                            <a href="/updateprofile/{{ auth()->id() }}/edit" class="dropdown-item p-3 d-flex"><span><i class="bi me-2 bi-person-fill"></i></span>Profile</a>
                            <a class="dropdown-item p-3 d-flex" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span><i class="bi me-2 bi-box-arrow-left"></i></span>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!-- End Navbar -->

    <!-- Left Sidebar -->
    <div class="left-menu bg-white">
        <div class="menubar-content">
            <nav class="animated bounceInDown">
                <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column list-unstyled p-0 m-0" id="sidebar">

                    {{-- Dashboard --}}
                    @can('dashboard.view')
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}"><i class="bi fs-5 bi-speedometer2"></i><span class="hide-menu">Dashboard</span></a>
                    </li>
                    @endcan

                    {{-- Appointments --}}
                    @can('appointments.view')
                    <li class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <a href="{{ route('appointments.index') }}"><i class="bi fs-5 bi-calendar-check"></i><span class="hide-menu">Appointments</span></a>
                    </li>
                    @endcan

                    {{-- Services --}}
                    @can('categories.view')
                    <li class="{{ request()->routeIs('category.*') ? 'active' : '' }} sub-menu" onclick="$('#nav-services').toggleClass('d-none')">
                        <a href="#"><i class="bi fs-5 bi-grid"></i><span class="hide-menu">Manage Services</span>
                        <i class="bi bi-caret-{{ request()->routeIs('category.*') ? 'down' : 'left' }}-fill right"></i></a>
                    </li>
                    <div id="nav-services" class="{{ request()->routeIs('category.*') ? '' : 'd-none' }}">
                        <ul class="submenu list-unstyled">
                            <li class="{{ request()->routeIs('category.*') ? 'active' : '' }}">
                                <a href="{{ route('category.index') }}"><i class="bi fs-5 bi-list-ul"></i><span class="hide-menu">Services</span></a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    {{-- Users --}}
                    @can('users.view')
                    <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}"><i class="bi fs-5 bi-people"></i><span class="hide-menu">Users</span></a>
                    </li>
                    @endcan

                    {{-- Access Control --}}
                    @if(auth()->user()->isSuperAdmin())
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Access Control</small>
                    </li>
                    @can('roles.view')
                    <li class="{{ request()->routeIs('role.*') ? 'active' : '' }}">
                        <a href="{{ route('role.index') }}"><i class="bi fs-5 bi-shield-lock"></i><span class="hide-menu">Roles</span></a>
                    </li>
                    @endcan
                    @can('permissions.view')
                    <li class="{{ request()->routeIs('permission.*') ? 'active' : '' }}">
                        <a href="{{ route('permission.index') }}"><i class="bi fs-5 bi-key"></i><span class="hide-menu">Permissions</span></a>
                    </li>
                    @endcan
                    @endif

                    {{-- Clinics --}}
                    @can('clinics.view')
                    <li class="{{ request()->routeIs('clinic.*') ? 'active' : '' }}">
                        <a href="{{ route('clinic.index') }}"><i class="bi fs-5 bi-building"></i><span class="hide-menu">Clinics</span></a>
                    </li>
                    @endcan

                    {{-- Prescriptions --}}
                    @can('prescriptions.view')
                    <li class="{{ request()->routeIs('prescription.*') ? 'active' : '' }}">
                        <a href="{{ route('prescription.index') }}"><i class="bi fs-5 bi-capsule"></i><span class="hide-menu">Prescriptions</span></a>
                    </li>
                    @endcan

                    {{-- Blogs --}}
                    @can('blogs.view')
                    <li class="{{ request()->routeIs('blogger.*') ? 'active' : '' }}">
                        <a href="{{ route('blogger.index') }}"><i class="bi fs-5 bi-newspaper"></i><span class="hide-menu">Blogs</span></a>
                    </li>
                    @endcan

                    {{-- Media --}}
                    @can('media.view')
                    <li class="{{ request()->routeIs('media.*') ? 'active' : '' }}">
                        <a href="{{ route('media.index') }}"><i class="bi fs-5 bi-images"></i><span class="hide-menu">Media</span></a>
                    </li>
                    @endcan

                    {{-- Email Templates --}}
                    @can('email-templates.view')
                    <li class="{{ request()->routeIs('email.*') ? 'active' : '' }}">
                        <a href="{{ route('email.index') }}"><i class="bi fs-5 bi-envelope"></i><span class="hide-menu">Email Templates</span></a>
                    </li>
                    @endcan

                    {{-- Contacts --}}
                    @can('contacts.view')
                    <li class="{{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                        <a href="{{ route('contacts.index') }}"><i class="bi fs-5 bi-chat-left-text"></i><span class="hide-menu">Contacts</span></a>
                    </li>
                    @endcan

                    {{-- Settings --}}
                    @can('settings.view')
                    <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}"><i class="bi fs-5 bi-gear"></i><span class="hide-menu">Settings</span></a>
                    </li>
                    @endcan

                    {{-- ── Inventory & Procurement ── --}}
                    @canany(['vendors.view','products.view','inventory.view','purchase-requests.view','purchases.view','returns.view','damaged-products.view'])
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Inventory</small>
                    </li>
                    @endcanany

                    @can('vendors.view')
                    <li class="{{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                        <a href="{{ route('vendor.index') }}"><i class="bi fs-5 bi-truck"></i><span class="hide-menu">Vendors</span></a>
                    </li>
                    @endcan

                    @can('products.view')
                    <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}"><i class="bi fs-5 bi-box-seam"></i><span class="hide-menu">Products</span></a>
                    </li>
                    @endcan

                    @can('inventory.view')
                    <li class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <a href="{{ route('inventory.index') }}"><i class="bi fs-5 bi-boxes"></i><span class="hide-menu">Inventory</span></a>
                    </li>
                    @endcan

                    @can('purchase-requests.view')
                    <li class="{{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
                        <a href="{{ route('purchase-requests.index') }}"><i class="bi fs-5 bi-cart-plus"></i><span class="hide-menu">Purchase Requests</span></a>
                    </li>
                    @endcan

                    @can('purchases.view')
                    <li class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <a href="{{ route('purchases.index') }}"><i class="bi fs-5 bi-bag-check"></i><span class="hide-menu">Purchases</span></a>
                    </li>
                    @endcan

                    @can('returns.view')
                    <li class="{{ request()->routeIs('returns.*') ? 'active' : '' }}">
                        <a href="{{ route('returns.index') }}"><i class="bi fs-5 bi-arrow-return-left"></i><span class="hide-menu">Returns</span></a>
                    </li>
                    @endcan

                    @can('damaged-products.view')
                    <li class="{{ request()->routeIs('damaged-products.*') ? 'active' : '' }}">
                        <a href="{{ route('damaged-products.index') }}"><i class="bi fs-5 bi-exclamation-triangle"></i><span class="hide-menu">Damaged Products</span></a>
                    </li>
                    @endcan

                    {{-- ── Clinical ── --}}
                    @canany(['doctor-agreements.view','appointment-products.view','consent-forms.view','call-logs.view','before-after-photos.view'])
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Clinical</small>
                    </li>
                    @endcanany

                    @can('doctor-agreements.view')
                    <li class="{{ request()->routeIs('doctor-agreements.*') ? 'active' : '' }}">
                        <a href="{{ route('doctor-agreements.index') }}"><i class="bi fs-5 bi-file-earmark-text"></i><span class="hide-menu">Doctor Agreements</span></a>
                    </li>
                    @endcan

                    @can('appointment-products.view')
                    <li class="{{ request()->routeIs('appointment-products.*') ? 'active' : '' }}">
                        <a href="{{ route('appointment-products.index') }}"><i class="bi fs-5 bi-bag-plus"></i><span class="hide-menu">Appt. Products</span></a>
                    </li>
                    @endcan

                    @can('consent-forms.view')
                    <li class="{{ request()->routeIs('consent-forms.*') ? 'active' : '' }}">
                        <a href="{{ route('consent-forms.index') }}"><i class="bi fs-5 bi-file-earmark-check"></i><span class="hide-menu">Consent Forms</span></a>
                    </li>
                    @endcan

                    @can('call-logs.view')
                    <li class="{{ request()->routeIs('call-logs.*') ? 'active' : '' }}">
                        <a href="{{ route('call-logs.index') }}"><i class="bi fs-5 bi-telephone"></i><span class="hide-menu">Call Logs</span></a>
                    </li>
                    @endcan

                    @can('before-after-photos.view')
                    <li class="{{ request()->routeIs('before-after-photos.*') ? 'active' : '' }}">
                        <a href="{{ route('before-after-photos.index') }}"><i class="bi fs-5 bi-images"></i><span class="hide-menu">Before/After Photos</span></a>
                    </li>
                    @endcan

                    {{-- ── Finance & HR ── --}}
                    @canany(['expenses.view','salaries.view'])
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Finance & HR</small>
                    </li>
                    @endcanany

                    @can('expenses.view')
                    <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <a href="{{ route('expenses.index') }}"><i class="bi fs-5 bi-cash-stack"></i><span class="hide-menu">Expenses</span></a>
                    </li>
                    @endcan

                    @can('salaries.view')
                    <li class="{{ request()->routeIs('salaries.*') ? 'active' : '' }}">
                        <a href="{{ route('salaries.index') }}"><i class="bi fs-5 bi-wallet2"></i><span class="hide-menu">Salaries</span></a>
                    </li>
                    @endcan

                    {{-- ── Tools ── --}}
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->canAny(['taxonomy.manage','imports.view','imports.create']))
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Tools</small>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('taxonomy.manage'))
                    <li class="{{ request()->routeIs('taxonomy.*') ? 'active' : '' }}">
                        <a href="{{ route('taxonomy.index') }}"><i class="bi fs-5 bi-arrow-left-right"></i><span class="hide-menu">Service Migration</span></a>
                    </li>
                    @endif
                    @canany(['imports.view','imports.create'])
                    <li class="{{ request()->routeIs('imports.*') ? 'active' : '' }}">
                        <a href="{{ route('imports.index') }}"><i class="bi fs-5 bi-file-earmark-arrow-up"></i><span class="hide-menu">CSV Import</span></a>
                    </li>
                    @endcanany

                    {{-- ── Reports ── --}}
                    @can('reports.view')
                    <li class="sidebar-section-label pt-2 pb-1">
                        <small class="text-uppercase text-muted px-3" style="font-size:10px;letter-spacing:1px;display:block">Analytics</small>
                    </li>
                    <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}"><i class="bi fs-5 bi-bar-chart-line"></i><span class="hide-menu">Reports</span></a>
                    </li>
                    @endcan

                </ul>
            </nav>
        </div>
    </div>
    <!-- End Left Sidebar -->

    <div class="content-wrapper">
        <div class="min-height-css">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>

<style>
@media only screen and (max-width: 576px) {
.left-menu {
    transition: .5s;
    position: fixed;
    z-index: 99;
    width:0;
}
}
</style>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" defer></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}" defer></script>
<script src="{{ asset('assets/libs/datatable/datatables.min.js') }}" defer></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>

@yield('script')

<script>
$(document).on('click', '#sidebar li', function() {
    $(this).addClass('active').siblings().removeClass('active')
});
$(document).ready(function() {
    $("#toggleSidebar").click(function() {
        $(".left-menu").toggleClass("hide");
        $(".content-wrapper").toggleClass("hide");
    });
    $("#toggleSidebar__").click(function() {
        $(".left-menu").toggleClass("hide");
        $(".content-wrapper").toggleClass("hide");
    });
});
</script>
<script src="{{ asset('custom.js') }}"></script>
