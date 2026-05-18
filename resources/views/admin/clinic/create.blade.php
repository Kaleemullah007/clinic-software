@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Create Clinic</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
            {{-- form for for create clinic --}}
            @include('flash-message')

            <form method="POST" id="clinic" action="{{ route('clinic.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="name" class="form-label fs-6">Name </label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="Name" value="{{ old('name') }}"
                            autocomplete="name" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="phone" class="form-label fs-6">Phone </label>
                        <input type="phone"
                            class="form-control bg-grey border-secondary @error('phone') is-invalid @enderror"
                            id="phone" name="phone" placeholder="+923001234567" value="{{ old('phone') }}"
                            autocomplete="phone" required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="address" class="form-label fs-6">Address </label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('address') is-invalid @enderror"
                            id="address" name="address" placeholder="Abc street, city" value="{{ old('address') }}"
                            autocomplete="address" required>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="support_email" class="form-label fs-6">Support_Email </label>
                        <input type="email"
                            class="form-control bg-grey border-secondary @error('support_email') is-invalid @enderror"
                            id="support_email" name="support_email" placeholder="abc123@example.com" value="{{ old('support_email') }}"
                            autocomplete="support_email" required>
                        @error('support_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="notification_email" class="form-label fs-6">Notification_Email </label>
                        <input type="email"
                            class="form-control bg-grey border-secondary @error('notification_email') is-invalid @enderror"
                            id="notification_email" name="notification_email" placeholder="abc123@example.com" value="{{ old('notification_email') }}"
                            autocomplete="notification_email" required>
                        @error('notification_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="status" class="form-label fs-6">Status</label><br>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                        @error('status')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="row mt-4">
                    <hr class=" border-secondary ">
                    <div class="col-12 pb-3">
                        <button type="submit" class="btn btn-success btn-md text-white"><i
                                class="bi bi-save me-2"></i> Save</button>
                        <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                class="bi bi-x-circle me-2"></i> Cancel</button>
                    </div>
                </div>
            </form>
            {{-- end form for create clinic --}}
            </div>
        </div>
    </div>
@endsection
