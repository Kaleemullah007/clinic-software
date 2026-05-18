@extends('layouts.app')
@section('content')

<section id="reset">
    <div class="bg-auth d-flex justify-content-center align-items-center">
        <div class="col-lg-3 col-md-6 col-10 bg-light rounded py-5 px-4">
            <div class="row justify-content-center">
                <div class="col-4">
                    <a href="/">

                        <img src="assets/images/logo2.jpg" class="img-thumbnail" alt="">
                    </a>
                </div>
                @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            </div>
            <!-- form start for Reset password -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="row justify-content-center">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},

                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>

                </div>
            </form>
            <!-- form end for Reset password -->
        </div>
    </div>
</section>
@endsection

