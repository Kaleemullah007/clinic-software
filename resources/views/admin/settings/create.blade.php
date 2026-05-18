@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Create Setting</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for create Setting --}}
                @include('flash-message')

                <form method="POST" action="" enctype="">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_name" class="form-label fs-6">Name</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('key_name') is-invalid @enderror"
                                id="key_name" name="key_name" placeholder="Name" value="{{ old('key_name') }}"
                                autocomplete="key_name" required>
                            @error('key_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_value" class="form-label fs-6">Value</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('key_value') is-invalid @enderror"
                                id="key_value" name="key_value" placeholder="Value"
                                value="{{ old('key_value') }}" autocomplete="key_value" required>
                            @error('key_value')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <input type="checkbox" id="status" name="status"
                             data-toggle="switchbutton" data-size="md" data-onstyle="success"
                                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3 pt-5">
                            <a href="#" class="btn btn-success "><i class="bi bi-plus-lg"></i> Add</a>
                            {{-- <input type="text"
                                class="form-control bg-grey border-secondary @error('key_value') is-invalid @enderror"
                                id="key_value" name="key_value" placeholder="Value"
                                value="{{ old('key_value') }}" autocomplete="key_value" required> --}}
                            @error('key_value')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mt-4">
                            <hr class=" border-secondary ">
                            <div class="col-12 pb-3">
                                <button type="button" class="btn btn-success btn-md text-white"><i
                                        class="bi bi-save me-2"></i> Save</button>
                                <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                        class="bi bi-x-circle me-2"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
                {{-- end form for create Setting --}}
            </div>
        </div>
    </div>
@endsection

