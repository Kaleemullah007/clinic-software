<?php include("includes/meta.php"); ?>
<?php include("includes/style.php"); ?>


    <!-- main-content start -->
    <section id="ForgotPassword">
        <div class="bg-auth d-flex justify-content-center align-items-center">
            <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4">
                <div class="row justify-content-center">
                    <div class="col-4">
                        <a href="index.php">

                            <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                        </a>
                    </div>
                    <div class="col-12 text-center pt-2">
                        <h1>Reset Password</h1>
                    </div>
                </div>
                <!-- form start for Reset password -->
                <form method="POST" action="" enctype="">
                    <div class="row justify-content-center">
                        <div class="col-12 pt-2">
                            <label for="resetPassEmail" class="form-label">Email</label>
                            <input type="text" class="form-control bg-grey border-dark @error('resetPassEmail') is-invalid @enderror" placeholder="abc123@example.com" name="resetPassEmail" id="resetPassEmail" value="{{ old('resetPassEmail') }}" autocomplete="resetPassEmail" required autofocus>
                            <!-- @error('resetPassEmail')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror -->
                        </div>
                        <div class="col-12 pt-4 d-flex justify-content-center">
                            <button class="btn btn-primary w-100">Send Password Reset Link</button>
                        </div>
                        <div class="col-12 pt-2 text-center">
                            <span><a href="log-in.php" class="text-decoration-none link-dark"><small>Log In !</small></a></span>
                        </div>
                    </div>
                </form>
                <!-- form end for Reset password -->
            </div>
        </div>
    </section>
    <!-- main-content end -->

<?php include("includes/body-closing.php"); ?>
