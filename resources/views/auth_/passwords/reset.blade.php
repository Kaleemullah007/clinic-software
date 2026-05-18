@extends('layouts.app')

@section('content')

<section id="reset">
    <div class="bg-auth d-flex justify-content-center align-items-center">
        <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4">
            <div class="row justify-content-center">
                <div class="col-4">
                    <a href="/">

                        <img src="/assets/images/logo2.jpg" class="img-thumbnail" alt="">
                    </a>
                </div>
                <div class="col-12 text-center pt-2">
                    <h1>{{ __('Reset Password') }}</h1>
                </div>
            </div>
            <!-- form start for Reset password -->
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
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

                    <div class="col-12 pt-4 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                    <div class="col-12 pt-2 text-center">
                        <span><a href="{{route('login')}}" class="text-decoration-none link-dark"><small>Log In !</small></a></span>
                    </div>
                </div>
            </form>
            <!-- form end for Reset password -->
        </div>
    </div>
</section>

{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
