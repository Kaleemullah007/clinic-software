@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="log-in d-flex justify-content-center align-items-center bg-image">
        <div class="sign-in-css w-sm-100 rounded bg-light">

            <div class="row text-center my-4">
                <div class="logo-container">
                    <img src="{{asset('public/assets/images/rktech-logo.jpg')}}" class="rounded w-25" alt="">
                </div>
            </div>
            <div class="row text-center">
                <h3>{{ __('en.Sign-In') }} </h3>
            </div>
            <div class="row m-3 ">
                <div class="col-12">
                    <label for="email-address" class="form-label fs-6">{{ __('en.Email Address') }}</label>
                </div>
                <div class="col-12">
                    <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email','') }}" required autocomplete="email" autofocus placeholder="Abc123@example.com" aria-label="email-address">

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
            </div>
            <div class="row m-3 ">
                <div class="col-12">
                    <label for="password" class="form-label fs-6">{{ __('en.Password') }}</label>
                </div>
                <div class="col-12">
                    <input type="password" id="password" name="password" value="" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="******" aria-label="password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('en.Remember Me') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mt-4 justify-content-center">
                <div class="col-8">
                    <button type="submit" class="btn btn-success rounded-pill w-100">
                        {{ __('en.Sign In') }}
                    </button>
                    {{-- <a href="#" class="btn btn-success rounded-pill w-100">Sign In</a> --}}
                </div>
            </div>
            <div class="row my-2 text-center">
                @if (Route::has('password.request'))
                <a class="anchor-css text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('en.Forgot Your Password?') }}
                </a>
                @endif

            </div>
            <div class="row mb-4 justify-content-center">
                <div class="col-8">
                    <a href="{{ langUrl('register') }}" class="btn btn-danger rounded-pill w-100">
                        {{ __('en.Sign Up') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

