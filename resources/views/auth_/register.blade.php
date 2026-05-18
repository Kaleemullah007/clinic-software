@extends('layouts.app')

@section('content')
 <!-- main-content start -->
 <section id="SignUp">
    <div class="bg-auth d-flex justify-content-center align-items-center">
        <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4">
            <div class="row justify-content-center">
                <div class="col-4">
                    <a href="/">

                        <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                    </a>
                </div>
                <div class="col-12 text-center pt-2">
                    <h1>Sign Up</h1>
                </div>
            </div>
            <!-- form start for sign up -->
            <form method="POST" action="{{ route('register') }}" enctype="">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-12">
                        <label for="name" class="form-label">Username</label>
                        <input type="text" name="name" class="form-control bg-grey border-dark @error('name') is-invalid @enderror" placeholder="Username" id="name" value="{{ old('name')}}" autocomplete="name" required autofocus>
                       @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-12 pt-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" class="form-control bg-grey border-dark @error('email') is-invalid @enderror" placeholder="abc123@example.com"  id="email" value="{{ old('email')}}" autocomplete="email" required>
                         @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-12 pt-2">
                        <label for="password" class="form-label">Password</label>
                        <input type="Password" name="password" class="form-control bg-grey border-dark @error('password') is-invalid @enderror" placeholder="********"  id="password" value="{{ old('password')}}" autocomplete="password" required>
                         @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-12 pt-2">
                        <label for="password-confirm"  class="form-label">Confirm Password</label>
                        <input type="Password" name="password_confirmation" class="form-control bg-grey border-dark @error('password-confirm') is-invalid @enderror" placeholder="********" id="password-confirm" value="{{ old('password-confirm')}}" autocomplete="password-confirm" required>
                         @error('password-confirm')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-8 pt-4 d-flex justify-content-center">
                        <button class="btn btn-danger w-100">Create Account</button>
                    </div>
                    <div class="col-12 pt-2 text-center">
                        <span><small>Already have an Account !</small></span>
                    </div>
                    <div class="col-8 pt-2">
                        <a href="{{route('login')}}" class="btn btn-success w-100">Log In</a>
                    </div>
                </div>
            </form>
            <!-- form end for sign up -->
        </div>
    </div>
</section>
<!-- main-content end -->
@endsection
