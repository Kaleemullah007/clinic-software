<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="@yield('description','description')">
    <meta name="keywords" content="@yield('keywords','keywords')">
    <meta name="author" content="@yield('author','author')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title',config('app.name', 'Clinic'))</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/icon.jpg">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>


    {{-- Nav --}}
    @include("includes/header")
    {{-- End nav --}}

    @yield('content')


    {{-- footer --}}
    @include("includes/footer")
    {{-- End footer --}}

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script>
    $(document).ready(function(){
		// $("#myModal").modal('show');
	});
    $(document).ready(function() {

                    getAppointments();


                $('.select2').select2()
});

</script>
