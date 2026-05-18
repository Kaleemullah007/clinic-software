<div class="carousel-inner ">
    @foreach (config('mubashir.mobile_slider') as $key => $image )
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