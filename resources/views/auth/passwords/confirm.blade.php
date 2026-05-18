@extends('layouts.app')

@section('content')



<div class="d-flex justify-content-center align-items-center bg-image">
    <div class=" w-25 rounded bg-light mt-5">
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="row text-center my-4">
                <div class="logo-container">
                    <img src="/public/assets/images/rktech-logo.jpg" class="rounded w-25" alt="">
                </div>
            </div>

            <div class="row text-center">
                <h3>    {{ __('en.Please confirm your password before continuing.') }}</h3>
            </div>
            <div class="row m-3 ">
                <div class="col-12">
                    <label for="email-address" class="form-label fs-6">{{ __('en.Password') }}</label>
                </div>
                <div class="col-12">
                    <input id="password" type="password" autofocus placeholder="*******" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-8">
                    <button type="submit" class="btn btn-success rounded-pill w-100">
                        {{ __('en.Confirm Password') }}
                    </button>
                </div>
            </div>

            @if (Route::has('password.request'))
            <div class="row my-2 text-center">
                <a class="banchor-css" href="{{ route('password.request') }}">
                    {{ __('en.Forgot Your Password?') }}
                </a>
            </div>
            @endif

        </form>
    </div>


</div>

@endsection
