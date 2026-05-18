@extends('layouts.admin')

@section('content')

<!-- main-content start -->
<div class="container-fluid">
        <div class="container">
            <div class="row pt-3">
                <div class="col-12">
                    <h4>Contact</h4>
                </div>
                <hr>
            </div>

            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-12 mt-2 d-flex ">
                    <label for="search" class="form-label mt-1"><i class="bi bi-search "></i></label>
                    <input type="text" class="form-control bg-grey form-control-css border-secondary ms-3 rounded"
                        placeholder="Search this table..." id="search">
                </div>
                <div class="col-lg-9 col-md-6 col-12 mt-2 text-end">
                    <!-- offcanvas trigger for filter -->
                    {{-- <button type="button" class="btn btn-sm me-2 btn-outline-primary" data-bs-toggle="offcanvas"
                        data-bs-target="#filters" aria-controls="filters"><i class="bi bi-funnel"></i> Filter</button>
                    <button type="button" class="btn btn-sm me-2 btn-outline-success"><i class="bi bi-filetype-pdf"></i>
                        PDF</button>
                    <button type="button" class="btn btn-sm me-2 btn-outline-danger"><i
                            class="bi bi-file-earmark-excel-fill"></i> EXCEL</button> --}}
                    <!-- modal trigger for create plan -->
                    {{-- <button type="button" data-bs-toggle="modal" data-bs-target="#createcontact" class="btn btn-sm me-2 btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Create</button> --}}
                </div>
            </div>
            @include('flash-message')
            <div class="table-responsive">
                @include('admin.contact.ajax-contact',$contacts)

            </div>
        </div>

        @include('admin.contact.reply')
        @include('admin.contact.details')

    <!-- offcanvas itself for filter -->
    {{-- <div class="offcanvas offcanvas-end" tabindex="-1" id="filters" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            <h5 class="offcanvas-title me-5" id="offcanvasExampleLabel">Filters</h5>
        </div>
        <form method="POST" action="" enctype="">
            <div class="offcanvas-body">
                <label for="planCode" class="form-label">Plan Code</label>
                <input type="text"
                    class="form-control bg-grey border-secondary @error('planCode') is-invalid @enderror" id="planCode"
                    name="planCode" placeholder="Search by Code" value="{{ old('planCode') }}" autocomplete="planCode">
                @error('planCode')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <label for="planName" class="form-label mt-3">Plan Name</label>
                <input type="text"
                    class="form-control bg-grey border-secondary @error('planName') is-invalid @enderror" id="planName"
                    name="planName" placeholder="Search by Name" value="{{ old('planName') }}" autocomplete="planName">
                @error('planName')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <label for="planValidity" class="form-label mt-3">Validity</label>
                <select class="form-select bg-grey border-secondary @error('planValidity') is-invalid @enderror"
                    id="planValidity" name="planValidity" autocomplete="planValidity">
                    <option>Choose</option>
                    <option value="1" @if (old('planValidity') == 1) 'selected' @endif>One Month</option>
                    <option value="2" @if (old('planValidity') == 2) 'selected' @endif>One Year</option>
                    <option value="2" @if (old('planValidity') == 3) 'selected' @endif>Unlimited</option>
                </select>
                @error('planValidity')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <label for="planStatus" class="form-label mt-3">Status</label>
                <select class="form-select bg-grey border-secondary @error('planStatus') is-invalid @enderror"
                    id="planStatus" name="planStatus" autocomplete="planStatus">
                    <option>Choose</option>
                    <option value="1" @if (old('planStatus') == 1) 'selected' @endif>Active</option>
                    <option value="2" @if (old('planStatus') == 2) 'selected' @endif>Inactive</option>
                </select>
                @error('planStatus')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="row py-4">
                    <div class="col-6">
                        <button class="btn btn-success rounded btn-sm w-100" type="button"><i class="bi bi-funnel"></i>
                            Filter</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-danger rounded btn-sm w-100" type="button"><i class="bi bi-x-circle"></i>
                            Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div> --}}

</div>

@endsection
