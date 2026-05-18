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
            <form method="POST" action="{{ route('password.email') }}">
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
                    <div class="col-12 pt-4 d-flex justify-content-center">
                        <button type="submit" class="btn btn-success">
                            {{ __('Send Password Reset Link') }}
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
@endsection
