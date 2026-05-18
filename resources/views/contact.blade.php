@extends('layouts.front')

@section('content')
 <!-- main-content start -->
 <section id="Contact-us">
    <div class="row my-4 mx-3 justify-content-center">
        <div class="col-12 text-center fadeIn">
            <p class="text-orange fs-1 mb-1 fw-bold">Contact Us</p>
            <p class="text-theme fs-1 fw-bold">Feel Free to Contact Us</p>
        </div>
        <div class="col-lg-5 bg-ltheme ps-3 py-5 text-theme mt-3 fadeInn">
            <p class="h1 fw-bold">Get In Touch</p>
            <div class="pt-5 d-flex">
                <i class="bi bi-geo-alt-fill pe-1 fs-5"></i>
                <p class="fs-5">{{config('mubashir.address')}}</p>
            </div>
            <div class="pt-3">
                <i class="bi bi-envelope-open-fill pe-1 fs-5"></i><span class="fs-5">{{config('mubashir.email')}}</span>
            </div>
            <div class="pt-3">
                <i class="bi bi-telephone pe-2 fs-5"></i><span class="fs-5"><a class="text-theme text-decoration-none" href="tel:{{config('mubashir.phone')}}">{{config('mubashir.phone')}}</a></span>
            </div>
        </div>
        <div class="col-lg-7 bg-theme text-ltheme mt-3 fadeInn">
            <!-- start form for message -->
            @if(session()->has('message'))
            <br>
            <div class="alert alert-secondary text-center bg-theme text-white" >
                {{ session()->get('message') }}
            </div>
            @endif
            <form method="POST" action="{{route('contactus')}}" enctype="">
                @csrf
                <input type="hidden" name="cp_form_name" value="contact_us" >
                <div class="row px-2 justify-content-center">
                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                        <input type="text" class="form-control bg-ltheme border-dark @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name" value="{{ old('name') }}" autocomplete="name" required>
                         @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                    </div>
                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                        <input type="text" class="form-control bg-ltheme border-dark @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                    </div>
                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                        <input type="text" class="form-control bg-ltheme border-dark @error('subject') is-invalid @enderror" id="subject" name="subject" placeholder="Subject" value="{{ old('subject') }}" autocomplete="subject" required>
                         @error('subject')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                    </div>
                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                        <input type="text" class="form-control bg-ltheme border-dark @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Phone" value="{{ old('phone') }}" autocomplete="phone" required>
                         @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                    </div>
                    <div class="col-12 pt-5 input-group-lg">
                        <textarea class="form-control bg-ltheme border-dark @error('message') is-invalid @enderror" id="message" name="message" placeholder="Message" autocomplete="message" rows="2" required>{{ old('message') }}</textarea>
                         @error('message')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                    </div>

                    <div class="col-12 mb-3 mt-5 d-flex justify-content-center">
                        <img src="{{route('image')}}" alt="Random Code" class="text-center mb-3 w-100 rounded">
                    </div>


                    <div class="col-12 mb-3 mt-3 input-group-lg">
                        <input type="text" name="txtSpamCode"  value="{{old('txtSpamCode')}}" placeholder="Enter above code" id="txtSpamCode" value="" maxlength="5" size="15" autocomplete="off" rel="" class="form-control bg-ltheme border-dark @error('txtSpamCode') is-invalid @enderror" />
                        @error('txtSpamCode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-12 pb-5 pt-4">
                        <button type="submit" class="btn btn-lg btn-orange w-100 fs-5 fw-bold">Send Message</button>
                    </div>
                </div>
            </form>
            <!-- end form for message -->
        </div>
        <div class="col-lg-12 d-flex justify-content-center px-0 mt-3 fadeInn">
            {{-- map embeded below it can be changed by embed-map.com --}}
            <div style="max-width:100%;list-style:none; transition: none;overflow:hidden;width:100%;height:500px;">
                <div id="embedded-map-display" style="height:100%; width:100%;max-width:100%;"><iframe style="height:100%;width:100%;border:0;" frameborder="0" src="https://www.google.com/maps/embed/v1/place?q=Blue+Area,+Islamabad,+Pakistan&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"></iframe></div><a class="googl-ehtml" href="https://www.bootstrapskins.com/themes" id="inject-map-data">premium bootstrap themes</a>
                <style>
                    #embedded-map-display img {
                        max-height: none;
                        max-width: none !important;
                        background: none !important;
                    }
                </style>
            </div>
        </div>
</section>
<!-- main-content end -->
@endsection
