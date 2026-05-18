<?php include("includes/meta.php"); ?>
<?php include("includes/style.php"); ?>

    <!-- main-content start -->
    <section id="SignUp">
        <div class="bg-auth d-flex justify-content-center align-items-center">
            <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4">
                <div class="row justify-content-center">
                    <div class="col-4">
                        <a href="index.php">

                            <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                        </a>
                    </div>
                    <div class="col-12 text-center pt-2">
                        <h1>Sign Up</h1>
                    </div>
                </div>
                <!-- form start for sign up -->
                <form method="POST" action="" enctype="">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <label for="signupName" class="form-label">Username</label>
                            <input type="text" class="form-control bg-grey border-dark @error('signupName') is-invalid @enderror" placeholder="Username" name="signupName" id="signupName" value="{{ old('signupName')}}" autocomplete="signupName" required autofocus>
                            <!-- @error('signupName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror -->
                        </div>
                        <div class="col-12 pt-2">
                            <label for="signupEmail" class="form-label">Email</label>
                            <input type="text" class="form-control bg-grey border-dark @error('signupEmail') is-invalid @enderror" placeholder="abc123@example.com" name="signupEmail" id="signupEmail" value="{{ old('signupEmail')}}" autocomplete="signupEmail" required>
                            <!-- @error('signupEmail')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror -->
                        </div>
                        <div class="col-12 pt-2">
                            <label for="signupPass" class="form-label">Password</label>
                            <input type="Password" class="form-control bg-grey border-dark @error('signupPass') is-invalid @enderror" placeholder="********" name="signupPass" id="signupPass" value="{{ old('signupPass')}}" autocomplete="signupPass" required>
                            <!-- @error('signupPass')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror -->
                        </div>
                        <div class="col-12 pt-2">
                            <label for="signupConfirmPass" class="form-label">Confirm Password</label>
                            <input type="Password" class="form-control bg-grey border-dark @error('signupConfirmPass') is-invalid @enderror" placeholder="********" name="signupConfirmPass" id="signupConfirmPass" value="{{ old('signupConfirmPass')}}" autocomplete="signupConfirmPass" required>
                            <!-- @error('signupConfirmPass')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror -->
                        </div>
                        <div class="col-8 pt-4 d-flex justify-content-center">
                            <button class="btn btn-danger w-100">Create Account</button>
                        </div>
                        <div class="col-12 pt-2 text-center">
                            <span><small>Already have an Account !</small></span>
                        </div>
                        <div class="col-8 pt-2">
                            <a href="log-in.php" class="btn btn-success w-100">Log In</a>
                        </div>
                    </div>
                </form>
                <!-- form end for sign up -->
            </div>
        </div>
    </section>
    <!-- main-content end -->
<?php include("includes/body-closing.php"); ?>
