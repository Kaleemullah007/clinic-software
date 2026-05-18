@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Create Blog</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for create blog --}}
                @include('flash-message')
                <form method="POST" action="{{ route('blogger.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="title" class="form-label fs-6">Title</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('title') is-invalid @enderror"
                                id="title" name="title" placeholder="Title" value="{{ old('title') }}">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="short_description" class="form-label fs-6">Short Description</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('short_description') is-invalid @enderror"
                                id="short_description" name="short_description" placeholder="Short Description" value="{{ old('short_description') }}">
                            @error('short_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="feature_image" class="form-label fs-6">Feature Image</label>
                            <input type="file"
                                class="form-control bg-grey border-secondary @error('feature_image') is-invalid @enderror"
                                id="feature_image" name="feature_image" value="{{ old('feature_image') }}">
                            @error('feature_image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <div class="d-flex mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status" checked>
                                    <label for="status" class="form-label fs-6">Draft</label>
                                </div>
                                <div class="form-check ms-5">
                                    <input class="form-check-input" type="radio" name="status" id="publish" >
                                    <label for="publish" class="form-label fs-6">Publish</label>
                                </div>
                                <div class="form-check ms-5">
                                    <input class="form-check-input" type="radio" name="status" id="inactive" >
                                    <label for="inactive" class="form-label fs-6">Inactive</label>
                                </div>
                            </div>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 pt-3">
                            <label for="long_description" class="form-label fs-6">Long Description</label>
                            <textarea class="@error('long_description') is-invalid @enderror"
                                id="long_description" name="long_description">{{ old('long_description') }}</textarea>
                            @error('long_description')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
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
                {{-- end form for create blog --}}
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#long_description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
                      'bulletedList', 'numberedList', 'blockQuote', '|',
                      'link', 'insertTable', '|',
                      'outdent', 'indent', '|', 'undo', 'redo'],
            placeholder: 'Write your blog content here…'
        })
        .catch(error => console.error(error));
</script>
@endsection
