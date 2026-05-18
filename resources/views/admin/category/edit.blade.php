@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Edit Service</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                @include('flash-message')
                {{-- form for for Edit Service --}}
                <form method="POST" action="{{ route('category.update', $category->id) }}" enctype="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $category->id }}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="name" class="form-label fs-6">Name </label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                                id="name" name="name" placeholder="Name" value="{{ old('name', $category->name) }}"
                                autocomplete="name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="name" class="form-label fs-6">Price </label>
                            <input type="number"
                                class="form-control bg-grey border-secondary @error('price') is-invalid @enderror"
                                id="price" name="price" placeholder="Enter Price" value="{{ old('price',$category->price) }}"
                                autocomplete="price" required>
                            @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="is_parent" class="form-label fs-6">Is_Parent</label>
                            <select class="form-select bg-grey border-secondary @error('is_parent') is-invalid @enderror"
                                id="is_parent" name="is_parent" autocomplete="is_parent">
                                <option value="0" >Choose</option>
                                @foreach ($categories as $allcategory)

                                    <option value="{{ $allcategory->id }}"
                                        @if ($category->is_parent === $allcategory->id) selected @endif>{{ $allcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('is_parent')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="slug" class="form-label fs-6">Slug </label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('slug') is-invalid @enderror"
                                id="slug" name="slug" placeholder="slug" value="{{ old('slug', $category->slug) }}"
                                autocomplete="slug" required>
                            @error('slug')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <input type="checkbox" name="status" data-toggle="toggle" data-size="md"
                                data-onstyle="success" data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive"
                                @if ($category->status == 1) checked @endif>
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
                            <a href="{{ route('category.index') }}">
                                <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                        class="bi bi-x-circle me-2"></i> Cancel</button>
                            </a>
                        </div>
                    </div>
                </form>
                {{-- end form for Edit Service --}}
            </div>
        </div>
    </div>
@endsection
