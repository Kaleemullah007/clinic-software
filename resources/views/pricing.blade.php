@extends('layouts.front')

@php
    if (!isset($page_link)) {
        //    $page_link = 'pricing'request()->segment(2);
        $page = config('mubashir.pages') ?? null;
    }
    
    if ($page == null) {
        header('Location: ' . URL::to('/'));
        exit();
    }
    $allpages = $page;
    
@endphp

@section('content')
    <!-- main-content start -->
    <section id="Pricing">
        <div class="row justify-content-around mx-2 my-4 fadeIn">
            <div class="col-lg-6 col-12 pt-4">
                <p class="text-orange fs-1 fw-bold">Pricing Plans</p>
                <p class="text-theme fs-3 fw-bold">We Offer High Quality Skin Services for Very Fair Prices.</p>
                <p class="text-secondary fs-4 ">Ask your health query to our experienced Dermatologists online and receive instant medical advice and second opinion. <br>Get professional medical advice and second opinion now!</p>
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-6 col-12 pt-2">
                        <a href="appointment" class="nav-link"><button class="btn btn-orange fs-5 w-100">Make
                                Appointment</button></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-12 pt-4">
                <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel"
                    data-bs-interval="3000" data-bs-pause="false">
                    <div class="carousel-inner">

                        @php
                            $index = 0;
                            $is_active = 'active';
                        @endphp
                        @foreach ($allpages as $key => $allpage)
                            @php
                                $images = $allpage['images'] ?? [];
                            @endphp
                            @foreach ($images as $image)
                                <div class="carousel-item border-css {{ $is_active }}" style="height: 360px">
                                    <a href="{{ isset($image['link']) ? route($image['link']) : '' }}"
                                        class="{{ $image['linkclass'] ?? '/' }}">
                                        <img src="{{ $image['src'] ?? '' }}" style="max-height: 200px"
                                            class="h-100 img-responsive w-100" alt="Image">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-12 col-12 text-center pb-5 px-5">
                                                <h4 class="fw-bold text-orange pt-3">{{ $image['price'] }}</h4>
                                                @if ($image['is_discount'])
                                                    <h3 class="fw-bold text-orange strike"> {{ $image['discounted_price'] }}
                                                    </h3>
                                                @endif
                                                <h2 class="fw-bold text-theme">{{ substr($image['heading'], 0) ?? '' }}</h2>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @php
                                    if ($index == 0) {
                                        $index = 1;
                                        $is_active = '';
                                    }
                                @endphp
                            @endforeach
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mx-2">
            <div class="col-12 text-center">
                <h1 class="text-orange fw-bold">Our Prices</h1>
            </div>
            @foreach ($allpages as $key => $allpage)
                @php
                    $images = $allpage['images'] ?? [];
                @endphp
                @foreach ($images as $key => $image)
                    <div class="col-lg-3 col-md-6 col-12 mt-4">
                        <div class="card border-css">
                            <a href="{{ isset($image['link']) ? route($image['link']) : '' }}"
                                class="{{ $image['linkclass'] ?? '' }}">
                                <img class="{{ $image['class'] ?? 'card-img-top' }}" style="height: 240px"
                                    src="{{ $image['src'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}">
                            
                                <div class="card-body text-center">
                                    <h3 class="card-title">
                                        {{ substr($image['heading'], 0, 20) ?? '' }}{{ strlen($image['heading']) > 20 ? '...' : '' }}
                                    </h3>
                                    <h4 class="fw-bold text-orange "> {{ $image['price'] }}</h4>
                                    @if ($image['is_discount'])
                                        <h3 class="fw-bold text-orange strike"> {{ $image['discounted_price'] }} </h3>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </section>
    <!-- main-content end -->
@endsection

