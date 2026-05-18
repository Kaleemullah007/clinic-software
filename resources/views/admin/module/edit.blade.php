@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12">
            <h4>Edit Module</h4>
        </div>
        <hr class="my-0">
    </div>
    <div class="shadow-css my-3">
        <div class="px-3 pb-3">
            {{-- form for for Edit module --}}
            @include('flash-message')

            <form method="POST" id="module" enctype="">
                @csrf
                <div class="row flex-column">
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="name" class="form-label fs-6">Name </label>
                        <input type="text" class="form-control bg-grey border-secondary @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="name" class="form-label fs-6">Permissions </label>
                        <div class="row ms-2">
                            <div class="col-6  mt-2">
                                <h6>Permission 1</h6>
                            </div>
                            <div class="col-6">
                                <input type="checkbox" id="permission" name="permission" data-toggle="switchbutton" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
                                @error('permission')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row ms-2">
                            <div class="col-6  mt-2">
                                <h6>Permission 2</h6>
                            </div>
                            <div class="col-6">
                                <input type="checkbox" id="permission" name="permission" data-toggle="switchbutton" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
                                @error('permission')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row ms-2">
                            <div class="col-6  mt-2">
                                <h6>Permission 3</h6>
                            </div>
                            <div class="col-6">
                                <input type="checkbox" id="permission" name="permission" data-toggle="switchbutton" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
                                @error('permission')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <hr class=" border-secondary ">
                    <div class="col-12 pb-3">
                        <button type="submit" class="btn btn-success btn-md text-white"><i class="bi bi-save me-2"></i> Save</button>
                        <button type="button" class="btn btn-secondary btn-md ms-2"><i class="bi bi-x-circle me-2"></i> Cancel</button>
                    </div>
                </div>

            </form>
            {{-- end form for Edit module --}}
        </div>
    </div>
</div>
@endsection