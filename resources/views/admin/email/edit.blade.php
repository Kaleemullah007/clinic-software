@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Edit Email Template</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for edit email --}}
                @include('flash-message')

                {{-- <form method="POST" action="{{ route('email.update', $email->id) }}" enctype=""> --}}

                    @csrf
                    @method('PUT')
                    {{-- <input type="hidden" value="{{ $email->id }}" name="id"> --}}

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="name" class="form-label fs-6">Name</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="placeholder" class="form-label fs-6">Placeholder</label>
                            <div class="input-group mb-3">
                                <input type="text"
                                    class="form-control bg-grey border-secondary @error('Placeholder') is-invalid @enderror"
                                    id="placeholder" name="placeholder" value="{{ old('placeholder') }}">
                                @error('placeholder')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <button class="btn btn-success" type="button"data-bs-toggle="modal" data-bs-target="#editPlaceholder" id="button-addon2">Add</button>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="subject" class="form-label fs-6">Subject</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('Subject') is-invalid @enderror"
                                id="subject" name="subject" value="{{ old('subject') }}">
                            @error('subject')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="short_description" class="form-label fs-6">Short Description</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('short_description') is-invalid @enderror"
                                id="short_description" name="short_description" value="{{ old('short_description') }}">
                            @error('short_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 pt-3">
                            <label for="long_description" class="form-label fs-6">Long Description</label>
                            <textarea class="form-control bg-grey border-secondary @error('long_description') is-invalid @enderror"
                                id="long_description" name="long_description" value="{{ old('long_description') }}" style="height: 100px"
                                ></textarea>
                            @error('long_description')
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
                                Update</button>
                            <button type="button" class="btn btn-secondary btn-md ms-2"><i class="bi bi-x-circle me-2"></i>
                                Cancel</button>
                        </div>
                    </div>
                </form>
                {{-- end form for edit Page --}}
            </div>
        </div>
    </div>
@endsection

  
  {{-- modal itself for adding placeholder --}}
  <div class="modal fade" id="editPlaceholder" tabindex="-1" aria-labelledby="editPlaceholderLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editPlaceholderLabel">Add Placeholder</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="col-9">Jacob</td>
                        <td class="col-3">
                            <button class="btn btn-success btn-sm" type="button" id="button-addon2">Add</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-9">Jacob</td>
                        <td class="col-3">
                            <button class="btn btn-success btn-sm" type="button" id="button-addon2">Add</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-9">Jacob</td>
                        <td class="col-3">
                            <button class="btn btn-success btn-sm" type="button" id="button-addon2">Add</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>