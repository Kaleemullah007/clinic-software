@extends('layouts.app')
@section('content')
    <section id="logIn">
        <div class="bg-auth d-flex justify-content-center align-items-center">
            <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4 mt-5">
                <div class="row justify-content-center">
                    <div class="col-4">
                        <a href="/">
                            <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                        </a>
                    </div>
                    <div class="col-12 text-center pt-2">
                        <h1>Log In</h1>
                    </div>
                </div>
                <!-- form start for log in -->
                <form method="POST"  action="{{ route('login') }}" enctype="">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-12 pt-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control bg-grey border-dark @error('email') is-invalid @enderror" placeholder="abc123@example.com" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                             @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 pt-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"  class="form-control bg-grey border-dark @error('password') is-invalid @enderror" placeholder="********" name="password" id="password" value="{{ old('password') }}" autocomplete="password" required>
                            @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        </div>
                        <div class="col-12 pt-2 d-flex justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input border-dark" value="" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="flexCheckDefault">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-8 pt-2 d-flex justify-content-center">
                            <button class="btn btn-success w-100">Log In</button>
                        </div>
                        <div class="col-12 pt-2 text-center">
                            <span><a href="forgot-password" class="text-decoration-none link-dark"><small>
                                @if (Route::has('password.request'))
                                <a class="anchor-css" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                                @endif
                            </small></a></span>
                        </div>
                        <div class="col-8 pt-3">
                            <a href="register" class="btn btn-danger rounded-pill w-100">
                                {{ __('Sign Up') }}
                            </a>
                        </div>
                    </div>
                </form>
                <!-- form end for log in -->
            </div>
        </div>
    </section>
    <!-- main-content end -->
@endsection
