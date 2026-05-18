@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12">
            <h4>Create Permission</h4>
        </div>
        <hr class="my-0">
    </div>
    <div class="shadow-css my-3">
        <div class="px-3 pb-3">
            {{-- form for for create permission --}}
            @include('flash-message')

            <form method="POST" id="permission" enctype="">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="name" class="form-label fs-6">Name </label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('name') is-invalid @enderror" id="name"
                            name="name" placeholder="Name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 pt-3">
                        <label for="status" class="form-label fs-6">Status</label><br>
                        <input type="checkbox" id="status" name="status" data-toggle="switchbutton" data-size="md"
                            data-onstyle="success" data-offstyle="danger" data-onlabel="Active"
                            data-offlabel="Inactive">
                        @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="row mt-4">
                    <hr class=" border-secondary ">
                    <div class="col-12 pb-3">
                        <button type="submit" class="btn btn-success btn-md text-white"><i class="bi bi-save me-2"></i>
                            Save</button>
                        <button type="button" class="btn btn-secondary btn-md ms-2"><i class="bi bi-x-circle me-2"></i>
                            Cancel</button>
                    </div>
                </div>
            </form>
            {{-- end form for create permission --}}
        </div>
    </div>
</div>
@endsection