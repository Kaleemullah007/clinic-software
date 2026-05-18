@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-truck me-2 text-theme-color"></i>Add Vendor</h4>
            <a href="{{ route('vendor.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-5 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('vendor.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary"
                                   value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Company</label>
                            <input type="text" name="company" class="form-control border-secondary"
                                   value="{{ old('company') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control border-secondary"
                                   value="{{ old('phone') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control border-secondary"
                                   value="{{ old('email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control border-secondary" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme">
                            <i class="bi bi-save me-1"></i> Save Vendor
                        </button>
                        <a href="{{ route('vendor.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .btn-theme { background: linear-gradient(90deg, #B1083C, #d13729); color: #fff; border: none; }
    .shadow-css { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
</style>
@endsection
