@extends('layouts.front')

@section('content')
   <!-- main-content start -->
   <section id="About">
    <div class="text-center h0 text-theme pt-4 fadeIn">About Us</div>

    @include("includes/about-dr")

    
    <div class="row mx-2 fadeInn">
        <div class="col-lg-5 col-12 pt-5">
            <img src="assets/images/general-derm.png" class="img-thumbnail w-100" alt="">
        </div>
        <div class="col-lg-7 col-12 pt-5">
            <p class="text-orange fs-1 fw-bold">Our Staff</p>
            <p class="text-theme fs-2 fw-bold">Our Staff consists of Highly Qualified and Professional people with a lot of Experience in the Field.</p>
            <p class="text-secondary fs-5 fw-bold">Lorem ipsum dolor sit amet consectetur sit amet consectetur adipisicing elit. Facilis minus architecto labore, inventore commodi assumenda quaerat deserunt, debitis placeat dolorem at tempora ipsum perferendis eveniet.</p>
        </div>
    </div>
    <div class="row mx-2 pb-3 fadeInn">
        <div class="col-lg-7 col-12 pt-5">
            <p class="text-orange fs-1 fw-bold">Our Objective</p>
            <p class="text-theme fs-3 fw-bold">Our Objective is to Provide a very High Quality Medical Services to People in Very Fair Prices.</p>
            <p class="text-secondary fs-5 fw-bold">Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis minus architecto labore, inventore commodi assumenda quaerat deserunt, debitis placeat dolorem at tempora ipsum perferendis eveniet.</p>
            <p class="text-secondary fs-5 fw-bold">Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis minus architecto labore, inventore commodi assumenda quaerat deserunt, debitis placeat dolorem at tempora ipsum perferendis eveniet.</p>
        </div>
        <div class="col-lg-5 col-12 pt-5">
            <img src="assets/images/dermatology.png" class="img-thumbnail w-100" alt="">
        </div>
    </div>
</section>
<!-- main-content end -->
@endsection
