@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center bg-image">
        <div class=" w-25 rounded bg-light mt-5">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="row text-center my-4">
                    <div class="logo-container">
                        <img src="/public/assets/images/rktech-logo.jpg" class="rounded w-25" alt="">
                    </div>
                </div>

                <div class="row text-center text-start">
                    <h3>{{ __('en.Reset Password') }}</h3>
                    @if (session('status'))
                        <div class="row offset-1 col-10 alert alert-success justify-content-center " role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <div class="row m-3 ">
                    <div class="col-12">
                        <label for="email-address" class="form-label fs-6">{{ __('en.Email Address') }}</label>
                    </div>
                    <div class="col-12">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="Abc123@example.com" aria-label="email-address">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="row mt-4 justify-content-center">
                    <div class="col-8">
                        <button type="submit" class="btn btn-success rounded-pill w-100">
                            {{ __('en.Send Password Reset Link') }}
                        </button>
                    </div>
                </div>
                <div class="row my-2 text-center">
                    <a href="{{ route('login') }}" class="anchor-css text-decoration-none">{{ __('en.Sign-In') }}</a>
                </div>
            </form>
        </div>


    </div>
@endsection
