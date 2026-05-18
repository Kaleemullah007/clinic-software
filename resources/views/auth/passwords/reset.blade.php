@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="d-flex justify-content-center align-items-center bg-image">
            <div class=" w-25 rounded bg-light mt-3">
                <div class="row text-center my-4">
                    <div class="logo-container">
                        <img src="/public/assets/images/rktech-logo.jpg" class="rounded w-25" alt="">
                    </div>
                </div>

                <div class="row text-center">
                    <div class="card-header h3">{{ __('en.Reset Password') }}</div>
                </div>
                <div class="row m-2 ">
                    <div class="col-12">
                        <label for="email-address" class="form-label fs-6">Email Address</label>
                    </div>
                    <div class="col-12">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                            placeholder="Abc123@example.com" aria-label="email-address">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                </div>
                <div class="row m-2 ">
                    <div class="col-12">
                        <label for="new-password" class="form-label fs-6">New Password</label>
                    </div>
                    <div class="col-12">
                        <input id="password" type="password" aria-label="new-password" placeholder="******"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="row m-2 mb-3">
                    <div class="col-12">
                        <label for="re-enter-new-password" class="form-label fs-6">Re-Enter New Password</label>
                    </div>
                    <div class="col-12">


                        <input id="password-confirm" type="password" placeholder="******" aria-label="re-enter-new-password"
                            class="form-control @error('password') is-invalid @enderror" name="password_confirmation"
                            required autocomplete="new-password">
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                </div>
                <div class="row mt-4 justify-content-center">
                    <div class="col-8">
                        <button type="submit" class="btn btn-success rounded-pill w-100">
                            {{ __('en.Reset Password') }}
                        </button>
                    </div>
                </div>
                <div class="row my-2 text-center">
                    <a href="{{ route('login') }}" class="anchor-css">{{ 'Sign-In' }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
