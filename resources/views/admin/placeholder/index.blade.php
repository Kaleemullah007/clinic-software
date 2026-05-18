@extends('layouts.admin')

@section('content')

<!-- main-content start -->
<div class="container-fluid">
        <div class="container">
            <div class="row pt-3">
                <div class="col-12">
                    <h4>Placeholder</h4>
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
                    <a href="{{ route('placeholder.create') }}" class="btn btn-sm me-2 btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Create</a>
                </div>
            </div>
            @include('flash-message')
            <div class="table-responsive">
                @include('admin.placeholder.ajax-placeholder')

            </div>
        </div>

</div>

@endsection
