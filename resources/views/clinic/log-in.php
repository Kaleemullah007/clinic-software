<?php include("includes/meta.php"); ?>
<?php include("includes/style.php"); ?>


    <!-- main-content start -->
    <section id="logIn">
        <div class="bg-auth d-flex justify-content-center align-items-center">
            <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4 mt-5">
                <div class="row justify-content-center">
                    <div class="col-4">
                        <a href="index.php">
                            <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                        </a>
                    </div>
                    <div class="col-12 text-center pt-2">
                        <h1>Log In</h1>
                    </div>
                </div>
                <!-- form start for log in -->
                <form method="POST" action="" enctype="">
                    <div class="row justify-content-center">
                        <div class="col-12 pt-2">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="text" class="form-control bg-grey border-dark @error('loginEmail') is-invalid @enderror" placeholder="abc123@example.com" name="loginEmail" id="loginEmail" value="{{ old('loginEmail') }}" autocomplete="loginEmail" required autofocus>
                            <!-- @error('loginEmail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                        </div>
                        <div class="col-12 pt-2">
                            <label for="loginPass" class="form-label">Password</label>
                            <input type="Password" class="form-control bg-grey border-dark @error('loginPass') is-invalid @enderror" placeholder="********" name="loginPass" id="loginPass" value="{{ old('loginPass') }}" autocomplete="loginPass" required>
                            <!-- @error('loginPass')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror -->
                        </div>
                        <div class="col-12 pt-2 d-flex justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input border-dark" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-8 pt-2 d-flex justify-content-center">
                            <button class="btn btn-success w-100">Log In</button>
                        </div>
                        <div class="col-12 pt-2 text-center">
                            <span><a href="forgot-password.php" class="text-decoration-none link-dark"><small>Forgot Password?</small></a></span>
                        </div>
                        <div class="col-8 pt-3">
                            <a href="sign-up.php" class="btn btn-danger w-100">Sign Up</a>
                        </div>
                    </div>
                </form>
                <!-- form end for log in -->
            </div>
        </div>
    </section>
    <!-- main-content end -->
<?php include("includes/body-closing.php"); ?>
