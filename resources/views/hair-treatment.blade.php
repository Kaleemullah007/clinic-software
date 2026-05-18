@extends('layouts.home')

@section('content')

<!-- main-content start -->
    <section id="hairTreatment">
        <div class="container-fluid py-5 px-3">
            <div class="text-center">
                <span class="h1 fw-bold text-theme">Hair Treatment</span>
            </div>
            @include("includes/hairfall")
            @include("includes/hairtransplant")
        </div>
    </section>
<!-- main-content end -->

@endsection
