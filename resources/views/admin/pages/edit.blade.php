@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Edit Page</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for edit Page --}}
                @include('flash-message')
                <form method="POST" action="{{ route('pages.update', [$page->id]) }}" enctype="">

                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{ $page->id }}" name="id">

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="meta_tag" class="form-label fs-6">Meta Tag</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('meta_tag') is-invalid @enderror"
                                id="meta_tag" name="meta_tag" value="{{ old('meta_tag', $page->meta_tag) }}"
                                autocomplete="meta_tag" required>
                            @error('meta_tag')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="meta_description" class="form-label fs-6">Meta Description</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('meta_description') is-invalid @enderror"
                                id="meta_description" name="meta_description"
                                value="{{ old('meta_description', $page->meta_description) }}"
                                autocomplete="meta_description" required>
                            @error('meta_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="keywords" class="form-label fs-6">Keywords</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('keywords') is-invalid @enderror"
                                id="keywords" name="keywords" value="{{ old('keywords', $page->keywords) }}"
                                autocomplete="keywords" required>
                            @error('keywords')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="heading" class="form-label fs-6">Heading</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('heading') is-invalid @enderror"
                                id="heading" name="heading" value="{{ old('heading', $page->heading) }}"
                                autocomplete="heading" required>
                            @error('heading')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="description" class="form-label fs-6">Description</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('description') is-invalid @enderror"
                                id="description" name="description" value="{{ old('description', $page->description) }}"
                                autocomplete="description" required>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="procedure_heading" class="form-label fs-6">Procedure Heading</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('Procedure_Heading') is-invalid @enderror"
                                id="procedure_heading" name="procedure_heading"
                                value="{{ old('procedure_heading', $page->procedure_heading) }}"
                                autocomplete="procedure_heading" required>
                            @error('procedure_heading')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="procedure_description" class="form-label fs-6">Procedure Description</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('procedure_description') is-invalid @enderror"
                                id="procedure_description" name="procedure_description"
                                value="{{ old('procedure_description', $page->procedure_description) }}"
                                autocomplete="procedure_description" required>
                            @error('procedure_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="title" class="form-label fs-6">Title</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $page->title) }}" autocomplete="title"
                                required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="category_id" class="form-label fs-6">Category Id</label>
                            <select class="form-select bg-grey border-secondary @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" autocomplete="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @if (old('category_id', $page->category_id) == $category->id) 'selected' @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="is_discounted" class="form-label fs-6">Is Discounted</label><br>
                            <input type="checkbox" id="is_discounted" name="is_discounted" data-toggle="switchbutton"
                                data-size="md" data-onstyle="success" data-offstyle="danger" data-onlabel="Yes"
                                data-offlabel="No" @if ($page->is_discounted == 1) checked @endif>
                            @error('is_discounted')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="price" class="form-label fs-6">Price</label>
                            <input type="number"
                                class="form-control bg-grey border-secondary @error('price') is-invalid @enderror"
                                id="price" name="price" value="{{ old('price', $page->price) }}"
                                autocomplete="price" required>
                            @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="discounted_price" class="form-label fs-6">Discounted Price</label>
                            <input type="number"
                                class="form-control bg-grey border-secondary @error('discounted_price') is-invalid @enderror"
                                id="discounted_price" name="discounted_price"
                                value="{{ old('discounted_price', $page->discounted_price) }}"
                                autocomplete="discounted_price" required>
                            @error('discounted_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="is_button_availalble" class="form-label fs-6">Is_Button_Availalble</label><br>
                            <input type="checkbox" id="is_button_availalble" name="is_button_availalble"
                                data-toggle="switchbutton" data-size="md" data-onstyle="success" data-offstyle="danger"
                                data-onlabel="Yes" data-offlabel="No" @if ($page->is_button_availalble == 1) checked @endif>
                            @error('is_button_availalble')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="url" class="form-label fs-6">URL</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('url') is-invalid @enderror"
                                id="url" name="url" value="{{ old('url', $page->url) }}" autocomplete="url"
                                required>
                            @error('url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="slug" class="form-label fs-6">Slug </label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('slug') is-invalid @enderror"
                                id="slug" name="slug" value="{{ old('slug', $page->slug) }}"
                                autocomplete="slug" required>
                            @error('slug')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <input type="checkbox" id="status" name="status" data-toggle="switchbutton"
                                data-size="md" data-onstyle="success" data-offstyle="danger" data-onlabel="Active"
                                data-offlabel="Inactive" @if ($page->status == 1) checked @endif>
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
                            <button type="submit" class="btn btn-success btn-md text-white"><i
                                    class="bi bi-save me-2"></i> Save</button>
                            <a href="{{ route('pages.index') }}" class="btn btn-secondary btn-md ms-2"><i
                                    class="bi bi-x-circle me-2"></i> Cancel</a>
                        </div>
                    </div>
                </form>
                {{-- end form for edit Page --}}
            </div>
        </div>
    </div>
@endsection
