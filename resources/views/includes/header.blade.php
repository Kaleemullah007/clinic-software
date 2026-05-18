
<body>
    <!-- topbar start -->
    <div class="container-fluid d-none d-lg-block">
        <div class="row align-items-center justify-content-around bg-ltheme text-theme fs-6">
            <div class="col">
                <span class=" ps-3 "><i class="bi bi-clock me-3"></i>Opening Hours : Mon - Sat : 9:00 am - 5:00 pm</span>
            </div>
            <div class="col ps-5 d-flex justify-content-center">
                <ul class="nav d-flex align-items-center">
                    <li class="nav-item fs-4 pe-2"><a href="https://instagram.com/drmubashirdaha" class="rounded-pill"><i class="bi bi-instagram instagram-color"></i></a></li>
                    <li class="nav-item fs-4 pe-2"><a href="https://www.facebook.com/drmubashirdaha/" class="rounded-pill"><i class="bi bi-facebook facebook-color"></i></a></li>
                    <li class="nav-item fs-5 pe-2 mt-1"><a href="https://vt.tiktok.com/ZSRdmoVdk/" class="rounded-pill"><i class="bi bi-tiktok tiktok-color"></i></a></li>
                    <li class="nav-item fs-4 pe-2"><a href="https://youtu.be/wSHp7SymJ-M" class="rounded-pill"><i class="bi bi-youtube youtube-color"></i></a></li>
                    <li class="nav-item fs-4 pe-2"><a href="https://www.snapchat.com/add/drmubashirdaha?share_id=ANubH7TP2gU&locale=en-PK" class="rounded-pill"><i class="bi bi-snapchat snapchat-color"></i></a></li>
                </ul>
            </div>
            <div class="col d-flex justify-content-end">
                <span class="pe-2"><i class="bi bi-envelope-open me-1"></i>{{config('mubashir.email')}}</span>
                <span class=""><a href="#" class="link-dark text-decoration-none"><i class="bi bi-telephone mx-1"></i><a class="text-black  text-decoration-none" href="tel:{{config('mubashir.phone')}}">{{config('mubashir.phone')}}</a></a></span>
            </div>
        </div>
    </div>
    <!-- topbar end -->

    <!-- Navbar start -->
    <nav class="navbar navbar-expand-lg bg-theme navbar-dark sticky-top py-0">
        <div class="container-fluid row justify-content-around">
            <div class="col-lg-2 col-12">
                <a class="navbar-brand fw-bold float-end" href="/"><span class="fs-2"><img src="{{config('mubashir.logo')}}" class="logo-css" alt="LOGO"></span></a>
                <button class="navbar-toggler float-start fs-1 mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="col-lg-10">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mt-1 pb-0 text-light ">
                        <li class="nav-item ms-3">
                            <a class="nav-link active fs-5" aria-current="page" href="/">Home</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-light fs-5" href="{{route('about')}}">About</a>
                        </li>
                        <li class="nav-item dropdown ms-3" id="dropdown-css">
                            <a class="nav-link text-light fs-5" data-bs-toggle="dropdown" aria-expanded="false">Skin Care Treatment<i class="bi bi-caret-down-fill ms-2"></i></a>
                            <ul class="dropdown-menu">
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('acne-scar')}}">Acne Scars <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('co2-fractional-laser')}}">CO2 Fractional Laser</a></li>
                                                <li><a class="dropdown-item" href="{{route('face-prp-micro-needlingandmesotherapy')}}">PRP for face + Micro Needling &  Mesotherapy</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('melasma-pigmentation-skin-glow')}}">Melasma / Pigmentation / Skin Glow <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('q-switch-laser')}}">Q-Switch Laser</a></li>
                                                <li><a class="dropdown-item" href="{{route('glutathione-cocktail')}}">Glutathione Cocktail</a></li>
                                                <li><a class="dropdown-item" href="{{route('prp-micro-needling-and-mesotherapy')}}">PRP + Micro Needling & Mesotherapy</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('hydra-facial')}}">Hydra Facial <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('basic-simple-hydrafacial')}}">Basic / Simple Hydrafacial</a></li>
                                                <li><a class="dropdown-item" href="{{route('oxygeno-facial')}}">Oxygeno Facial</a></li>
                                                <li><a class="dropdown-item" href="{{route('photo-facial')}}">Photo Facial</a></li>
                                            </ul>
                                        </li>
                                        <li><a class="dropdown-item" href="{{route('carbon-peel-laser')}}">Carbon Peel Laser</a></li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('fillers')}}">Fillers <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('lip-filler')}}">Lips Fillers</a></li>
                                                <li><a class="dropdown-item" href="{{route('laugh-nasolabial-lines')}}">Laugh Lines / Nasolabial Lines</a></li>
                                                <li><a class="dropdown-item" href="{{route('under-eye-filler')}}">Under Eye Fillers</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('botox')}}">Botox <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('forhead-lines')}}">Forehead Lines</a></li>
                                                <li><a class="dropdown-item" href="{{route('crows-feet')}}">Crow’s Feet</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item" href="{{route('non-surgical-face-lift')}}">Non Surgical Face Lift <i class="bi bi-caret-right-fill float-end ms-2"></i></a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{route('thread-face-lift')}}">Thread Face Lift</a></li>
                                                <li><a class="dropdown-item" href="{{route('high-intensity-focused-ultrasound')}}">High-intensity focused ultrasound (HIFU)</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{route('non-surgical-breast-lift')}}">Non Surgical Breast Lift</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{route('fat-reduction-fat-freezing-cryolipolysis')}}">Fat Reduction / Fat Freezing / Cryolipolysis</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{route('mole-removal')}}">Mole Removal</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{route('tattoo-removal')}}">Tattoo Removal by Laser</a>
                                        </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown ms-3" id="dropdown-css">
                            <a class="nav-link text-light fs-5" href="{{route('hair-treatment')}}" data-bs-toggle="dropdown"
                             aria-expanded="false">Hair Treatment<i class="bi bi-caret-down-fill ms-2"></i></a>
                            <ul class="dropdown-menu bg-custom-hair">
                                        <li><a class="dropdown-item" href="{{route('prp-for-hair-regrowth')}}">PRP for Hair Regrowth</a></li>
                                        <li><a class="dropdown-item" href="{{route('hair-transplant')}}">Hair Transplant</a></li>
                                        <li><a class="dropdown-item" href="{{route('laser-hair-removal')}}">Laser Hair Removal</a></li>
                            </ul>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-light fs-5" href="{{route('pricing')}}">Pricing</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-light fs-5" href="{{route('gallery')}}">Gallery</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-light fs-5" href="{{route('blogs')}}">Blogs</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-light fs-5" href="{{route('contact')}}">Contact us</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a href="{{route('appointment')}}" class="nav-link"><button class="btn btn-orange fs-6 fw-bold">Appointment</button></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar end -->
