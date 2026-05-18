@extends('layouts.home')

@section('content')

<!-- main-content start -->
    <section id="skinCareTreatment">
        <div class="container-fluid pt-3 px-3">
            <div class="text-center">
                <span class="h1 fw-bold text-theme">Skin Care Treatment</span>
            </div>

            @include("includes/acne-scars")
            @include("includes/mole-removal")
            @include("includes/forhead-lines")
            @include("includes/crows-feet")
            @include("includes/lip-filler")
            @include("includes/laugh-lines")
        </div>
    </section>
<!-- main-content end -->

@endsection
