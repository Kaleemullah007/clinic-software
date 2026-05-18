@extends('layouts.home')

@section('content')

<!-- main-content start -->
<div id="myModal1" class="modal fade in" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body bg-theme text-theme px-4 border border-3 rounded">
            <div class="d-flex justify-content-between">
                <h5 class="modal-title fw-bold"><img src="{{config('mubashir.logo')}}" class="logo-css me-3" alt="LOGO"></h5>
                <button type="button" class="btn-close btn-css p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="row">
                <div class="col-7 text-light">
                    <h3 class="pt-3 fw-bold">Autumn Season Offer</h3>
                    <span class="fs-6 fw-bold">Get a lot of DISCOUNTS on all of our Services and Treatments. So, Grab this exciting Offer Now.</span><br>
                    <p class="fs-6 fw-bold pt-3">Make an Appointment Right Now by Clicking the Button Below</p>
                    <div class="text-center"><a class="btn btn-orange w-100 fw-bold fs-5 ">Appointment</a></div>
                </div>
                <div class="col-5">
                    <div class="rainbow">
                        <img src="assets/images/injectables.png" class="image-fluid image-border h-100 w-100" alt="Image">
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>


<section id="Home">
    <div class=" d-lg-block d-md-block d-none" id="HomeSlider">
        <div id="carouselExampleCaptions" class="carousel slide carousel-dark" data-bs-ride="carousel" data-bs-interval="2500" data-bs-pause="false">
            <div class="carousel-inner">
                @foreach (config('mubashir.slider') as $key => $image )
                    <div class="carousel-item carousel-item-css {{
                        ($key==0)?'active':''}}">
                        <img src="{{$image}}" class="d-block w-100 custom-image" alt="Image">
                        <div class="carousel-caption carousel-caption-css d-block">
                            <div class="row justify-content-center mt-5 pt-5">
                                <div class="col-lg-8 col-md-8 col-10 pt-5">
                                    <h3 class="fw-bold py-3 text-theme">KEEP YOUR SKIN HEALTHY</h3>
                                    <h1 class="fw-bold py-3 text-theme">Take the Best Quality Skin Treatment by the World Class Dermatologist.</h1>
                                </div>
                            </div>
                            <div class="row justify-content-center pt-2">
                                <div class="col-lg-3 col-md-4 col-7 pt-3">
                                    <a href="#MakeAppointment" class="btn btn-lg btn-theme ms-2 px-4 py-3 fs-4 w-100">Appointment</a>
                                </div>
                                <div class="col-lg-3 col-md-4 col-7 pt-3">
                                    <a href="contact" class="btn btn-lg btn-orange ms-2 px-4 py-3 fs-4 w-100">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>            
           

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>


    {{-- Mobile slider --}}

    <div class="d-block d-lg-none d-md-none" id="HomeSlider">
        <div id="carouselExampleCaptions" class="carousel slide carousel-dark" data-bs-ride="carousel" data-bs-interval="500" data-bs-pause="false">
            @include("includes/mobile-slider")
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>



  
    <div class="row col-12 fadeInn justify-content-around text-light mb-4">
        <div class="col-lg-3 col-md-8 col-10 mt-5 bg-theme">
            <div class="row py-3 px-3">
                <div class="col-12 py-4">
                    <h2>Opening Hours</h2>
                    <div class="d-flex justify-content-between align-items-center pt-4">
                        <h5>Mon - Thu</h5>
                        <h6>9:00 AM - 5:00 PM</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-4">
                        <h5>Friday</h5>
                        <h6>3:00 PM - 10:00 PM</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-4">
                        <h5>Sat & Sun</h5>
                        <h6>Closed</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-8 col-10 mt-5 bg-ltheme text-theme">
            <div class="row py-3 px-3">
                <div class="col-12 py-4">
                    <h2>Our Objective</h2>
                    <div class="pt-3">
                        <h4>Our goal is to provide the best possible care for your skin so that you can look and feel your very best.</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-8 col-10 mt-5 bg-orange text-light">
            <div class="row py-3 px-3">
                <div class="col-12 py-4">
                    <h2>Make Appointment</h2>
                    <div class="pt-3">
                        <span class="fs-4">Fix an Appointment with a World class Skin Specialist (Dermatologist).</span>
                    </div>
                    <div class="pt-4">
                        <a href="#MakeAppointment" class="btn btn-light fw-bold fs-5 w-100">Make Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("includes/about-dr")

    <h1 class="text-center text-theme fw-bold">Our Specialities</h1>
        @include("includes/specialities")
    <div class="">
        @include("includes/make-appointment")
    </div>
</section>
<!-- main-content end -->

@endsection
