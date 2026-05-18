<?php include("includes/header.php"); ?>

    <!-- main-content start -->
    <section id="Contact-us">
        <div class="row my-4 mx-4 justify-content-center">
            <div class="col-12 text-center fadeIn">
                <p class="text-orange fs-1 mb-1 fw-bold">Contact Us</p>
                <p class="text-theme fs-1 fw-bold">Feel Free to Contact Us</p>
            </div>
            <div class="col-lg-5 bg-ltheme ps-5 py-5 text-theme mt-3 fadeInn">
                <p class="h1 fw-bold">Get In Touch</p>
                <div class="pt-5 d-flex">
                    <i class="bi bi-geo-alt-fill pe-3 fs-1"></i>
                    <p class="fs-3">123 Street, New York, USA</p>
                </div>
                <div class="pt-3">
                    <i class="bi bi-envelope-open-fill pe-4 fs-1"></i><span class="fs-3">ABC123@example.com</span>
                </div>
                <div class="pt-3">
                    <i class="bi bi-telephone pe-4 fs-1"></i><span class="fs-3">+123 456 12345</span>
                </div>
            </div>
            <div class="col-lg-7 bg-theme text-ltheme mt-3 fadeInn">
                <!-- start form for message -->
                <form method="POST" action="" enctype="">
                    <div class="row px-5 justify-content-center">
                        <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                            <input type="text" class="form-control bg-ltheme border-dark @error('conUsName') is-invalid @enderror" id="conUsName" name="conUsName" placeholder="Your Name" value="{{ old('conUsName') }}" autocomplete="conUsName" required>
                            <!-- @error('conUsName')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror -->
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                            <input type="text" class="form-control bg-ltheme border-dark @error('conUsEmail') is-invalid @enderror" id="conUsEmail" name="conUsEmail" placeholder="Your Email" value="{{ old('conUsEmail') }}" autocomplete="conUsEmail" required>
                            <!-- @error('conUsEmail')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror -->
                        </div>
                        <div class="col-12 pt-5 input-group-lg">
                            <input type="text" class="form-control bg-ltheme border-dark @error('conUsSubject') is-invalid @enderror" id="conUsSubject" name="conUsSubject" placeholder="Subject" value="{{ old('conUsSubject') }}" autocomplete="conUsSubject" required>
                            <!-- @error('conUsSubject')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror -->
                        </div>
                        <div class="col-12 pt-5 input-group-lg">
                            <textarea class="form-control bg-ltheme border-dark @error('conUsMessage') is-invalid @enderror" id="conUsMessage" name="conUsMessage" placeholder="Message" autocomplete="conUsMessage" rows="2" required>{{ old('conUsMessage') }}</textarea>
                            <!-- @error('conUsMessage')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror -->
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 py-5">
                            <button type="submit" class="btn btn-lg btn-orange w-100 fs-5 fw-bold">Send Message</button>
                        </div>
                    </div>
                </form>
                <!-- end form for message -->
            </div>
            <div class="col-lg-12 d-flex justify-content-center mt-5 fadeInn">
                <div style="max-width:100%;list-style:none; transition: none;overflow:hidden;width:1000px;height:500px;">
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

<?php include("includes/footer.php"); ?>
