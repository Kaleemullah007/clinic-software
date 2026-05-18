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
                    <h1>{{ __('Confirm Password') }}</h1>
                </div>
            </div>
            <!-- form start for Reset password -->
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                {{ __('Please confirm your password before continuing.') }}
                <div class="row justify-content-center">
                    <div class="col-12 pt-2">
                        <label for="password" class="form-label">Password</label>
                        <input type="Password" name="password" class="form-control bg-grey border-dark @error('password') is-invalid @enderror" placeholder="********"  id="password" value="{{ old('password')}}" autocomplete="password" required>
                         @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-12 pt-4 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Confirm Password') }}
                        </button>

                        @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif

                    </div>

                </div>
            </form>
            <!-- form end for Reset password -->
        </div>
    </div>
</section>

@endsection
