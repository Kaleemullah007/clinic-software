@extends('layouts.admin')

@section('content')

<!-- main-content start -->
<div class="container-fluid">
        <div class="container">
            <div class="row pt-3">
                <div class="col-12">
                    <h4>Media</h4>
                </div>
                <hr>
            </div>
            <div class="row ">
                <div class="col-lg-3 col-md-6 col-12 mt-2 d-flex ">
                    <label for="search" class="form-label mt-1"><i class="bi bi-search "></i></label>
                    <input type="text" class="form-control bg-grey form-control-css border-secondary ms-3 rounded"
                        placeholder="Search this table..." id="search">
                </div>
                <div class="col-lg-9 col-md-6 col-12 mt-2 text-end">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#createMedia" class="btn btn-sm me-2 btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Create</button>
                </div>
            </div>
            @include('flash-message')
            <div class="">
                @include('admin.media.ajax-media')

            </div>
        </div>
        {{-- @include('admin.media.edit') --}}
        @include('admin.media.create')


</div>

@endsection
