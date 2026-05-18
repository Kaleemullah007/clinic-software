    <!-- topbar start -->
    <div class="container-fluid d-none d-lg-block">
        <div class="row">
            <div class="col-6 bg-ltheme text-theme py-2 ps-5 fs-6">
                <span class=""><i class="bi bi-clock me-3"></i>Opening Hours : Mon - Sat : 9:00 am - 5:00 pm</span>
            </div>
            <div class="col-6 bg-ltheme text-theme py-2 pe-5 fs-6 d-flex justify-content-end">
                <span class="pe-5 border-end"><i class="bi bi-envelope-open me-2"></i>{{config('mubashir.email')}}</span>
                <span><i class="bi bi-telephone me-1"></i>+123-456-12345</span>
            </div>
        </div>
    </div>
    <!-- topbar end -->

    <!-- Navbar start -->
    <nav class="navbar navbar-expand-lg bg-theme navbar-dark sticky-top">
        <div class="container-fluid row justify-content-around">
            <div class="col-lg-2 col-12">
                <a class="navbar-brand fw-bold" href="index.php"><span class="fs-2 ms-5">Rk Tech</span></a>
                <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="col-lg-10">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-5 pb-0">
                        <li class="nav-item ms-3">
                            <a class="nav-link active fs-5" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link fs-5" href="about">About</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link fs-5" href="services.php">Services</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link fs-5" href="pricing.php">Pricing</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link fs-5" href="gallery.php">Gallery</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link fs-5" href="contact.php">Contact us</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a href="appointment.php" class="nav-link"><button class="btn btn-orange fs-6 fw-bold">Appointment</button></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar end -->
