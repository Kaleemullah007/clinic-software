@extends('layouts.front')

@section('content')

    <!-- main-content start -->
    <section id="Services">
        <div class="text-center h0 text-theme pt-5 fadeIn">Our Services</div>
        <div class="row m-5 fadeIn">
            <div class="col-lg-5 col-12 pt-5">
                <img src="assets/images/image4.png" class="img-thumbnail w-100" alt="">
            </div>
            <div class="col-lg-7 col-12 pt-5">
                <p class="text-orange fs-1 fw-bold">Services</p>
                <p class="text-theme fs-1 fw-bold">We Offer High Quality Skin Services</p>
                <p class="text-secondary fs-4 fw-bold">Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis minus architecto labore, inventore commodi assumenda quaerat deserunt, debitis placeat dolorem at tempora ipsum perferendis eveniet. <br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis minus architecto labore, inventore commodi assumenda quaerat deserunt, debitis placeat dolorem at tempora ipsum perferendis eveniet.</p>
            </div>
        </div>
        @include("includes.specialities")
        <div class="bg-image m-5 py-5 px-4 fadeInn">
            <div class="row justify-content-center py-5">
                <div class="col-lg-8 col-12 bg-ltheme">
                    <div id="carouselExampleCaptions" class="carousel slide carousel-dark" data-bs-ride="carousel" data-bs-interval="2500" data-bs-pause="false">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="row justify-content-center text-theme">
                                    <div class="col-lg-8 col-md-12 d-flex justify-content-center my-5">
                                        <div class="col-lg-4 col-md-6">
                                            <img src="assets/images/log-in3.jpg" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 text-center">
                                        <h2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, omnis deserunt. Ea aliquam rum.</h2>
                                    </div>
                                    <div class="col-lg-5 text-center my-5 border-top  border-secondary">
                                        <span class="h1">John Doe</span>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="row justify-content-center text-theme">
                                    <div class="col-lg-8 col-md-12 d-flex justify-content-center my-5">
                                        <div class="col-lg-4 col-md-6">
                                            <img src="assets/images/log-in.jpg" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 text-center">
                                        <h2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, omnis deserunt. Ea aliquam rum.</h2>
                                    </div>
                                    <div class="col-lg-5 text-center my-5 border-top  border-secondary">
                                        <span class="h1">John Doe</span>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="row justify-content-center text-theme">
                                    <div class="col-lg-8 col-md-12 d-flex justify-content-center my-5">
                                        <div class="col-lg-4 col-md-6">
                                            <img src="assets/images/img7.png" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 text-center">
                                        <h2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, omnis deserunt. Ea aliquam rum.</h2>
                                    </div>
                                    <div class="col-lg-5 text-center my-5 border-top  border-secondary">
                                        <span class="h1">John Doe</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev pe-5" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next ps-5" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- main-content end -->
@endsection
