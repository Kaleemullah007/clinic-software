
    <!-- footer start -->
    <div class="container-fluid bg-theme text-light">
        <footer class="row mt-3 px-5">
            <div class="col-lg-3 col-md-6 col-12 pt-5">
                <a href="/" class="d-flex align-items-center mb-3 link-light text-decoration-none">
                    <span class="fs-1 fw-bold"><img src="{{config('mubashir.logo')}}" class="logo-css me-3" alt="LOGO"></span>
                </a>
                <p class="fs-5 pe-2">Book an Appointment with World class Docter in the medical world in very fair prices.</p>
                <ul class="nav fs-1">
                    <li class="nav-item pe-3"><a href="https://www.facebook.com/drmubashirdaha/" class="rounded-pill link-light"><i class="bi bi-facebook"></i></a></li>
                    <li class="nav-item pe-3"><a href="https://www.linkedin.com/login" class="rounded-pill link-light"><i class="bi bi-linkedin"></i></a></li>
                    <li class="nav-item pe-3"><a href="https://instagram.com/drmubashirdaha" class="rounded-pill link-light"><i class="bi bi-instagram"></i></a></li>
                    <li class="nav-item pe-3"><a href="https://twitter.com/daha_mubashir?t=15KvxUUikhBJEHFsevfvjg&s=08" class="rounded-pill link-light"><i class="bi bi-twitter"></i></a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-12 pt-5">
                <h3>Popular Links</h3>
                <ul class="nav flex-column pt-3 fs-5">
                    <li class="nav-item mb-2"><a href="/" class="nav-link p-0 text-light">Home</a></li>
                    <li class="nav-item mb-2"><a href="appointment" class="nav-link p-0 text-light">Appointment</a></li>
                        {{-- <li class="nav-item mb-2"><a href="services" class="nav-link p-0 text-light">Services</a></li> --}}
                    <li class="nav-item mb-2"><a href="pricing" class="nav-link p-0 text-light">Pricing</a></li>
                    <li class="nav-item mb-2"><a href="contact" class="nav-link p-0 text-light">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-12 pt-5">
                <h3>Company</h3>
                <ul class="nav flex-column pt-3 fs-5">
                    <li class="nav-item mb-2"><a href="about" class="nav-link p-0 text-light">About Us</a></li>
                    <li class="nav-item mb-2"><a href="contact" class="nav-link p-0 text-light">Contact Us</a></li>
                    <li class="nav-item mb-2"><a href="/" class="nav-link p-0 text-light">Privacy Policy</a></li>
                    <li class="nav-item mb-2"><a href="/" class="nav-link p-0 text-light">FAQs</a></li>
                    <li class="nav-item mb-2"><a href="/" class="nav-link p-0 text-light">Terms of Use</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-12 pt-5">
                <h3>Get in touch</h3>
                <ul class="nav flex-column pt-3">
                    <li class="nav-item fs-6 mb-2"><i class="bi pe-2 bi-geo-alt-fill"></i><span>{{config('mubashir.address')}}</span></li>
                    <li class="nav-item fs-6 mb-2"><i class="bi pe-2 bi-envelope-open"></i><span>{{config('mubashir.email')}}</span></li>
                    <li class="nav-item fs-6 mb-2"><i class="bi pe-2 bi-telephone"></i><span>{{config('mubashir.phone')}}</span></li>

                </ul>
            </div>
            <div class="d-flex justify-content-center pb-2 mt-4 border-top fs-5">
                <p class="pt-3">&copy; {{date('Y')}} <a class="text-white text-decoration-none" href="https://www.upliftcom.com"> RKTech</a>, Inc. All rights reserved.</p>
            </div>
        </footer>
    </div>
    <!-- footer end -->


    
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

</body>

</html>
