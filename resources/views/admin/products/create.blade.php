@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2 text-theme-color"></i>Add Product</h4>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('products.store') }}" id="productForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select border-secondary">
                                <option value="1" {{ old('status',1)==1?'selected':'' }}>Active</option>
                                <option value="0" {{ old('status')==0?'selected':'' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="descriptionEditor" class="form-control border-secondary" rows="4">{{ old('description') }}</textarea>
                        </div>

                        {{-- Toggles --}}
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="has_variations" value="1"
                                       id="hasVariations" {{ old('has_variations') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="hasVariations">Has Variations</label>
                            </div>
                            <small class="text-muted">Enable if product comes in different sizes/types</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="track_inventory" value="1"
                                       id="trackInventory" checked {{ old('track_inventory',1) ? '' : '' }}>
                                <label class="form-check-label fw-semibold" for="trackInventory">Track Inventory</label>
                            </div>
                            <small class="text-muted">Deduct stock on appointment use</small>
                        </div>

                        {{-- Base price (hidden when variations on) --}}
                        <div class="col-md-4" id="basePriceWrap">
                            <label class="form-label fw-semibold">Base Price (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" min="0"
                                   class="form-control border-secondary @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Variations section --}}
                        <div class="col-12 d-none" id="variationsSection">
                            <label class="form-label fw-semibold">Variations</label>
                            <div id="variationsList">
                                <div class="variation-row d-flex gap-2 mb-2">
                                    <input type="text" name="variations[0][name]" placeholder="Variation name (e.g. 1 Session)"
                                           class="form-control border-secondary">
                                    <input type="number" name="variations[0][price]" placeholder="Price" step="0.01" min="0"
                                           class="form-control border-secondary" style="max-width:140px">
                                    <button type="button" class="btn btn-outline-danger btn-remove-var"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-theme mt-1" id="addVariationBtn">
                                <i class="bi bi-plus me-1"></i> Add Variation
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .btn-outline-theme{border-color:#B1083C;color:#B1083C;}
    .btn-outline-theme:hover{background:#B1083C;color:#fff;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>
let varIndex = 1;

function toggleVariations(){
    const on = $('#hasVariations').is(':checked');
    $('#variationsSection').toggleClass('d-none', !on);
    $('#basePriceWrap').toggleClass('d-none', on);
}

$(function(){
    toggleVariations();
    $('#hasVariations').on('change', toggleVariations);

    $('#addVariationBtn').on('click', function(){
        $('#variationsList').append(`
            <div class="variation-row d-flex gap-2 mb-2">
                <input type="text" name="variations[${varIndex}][name]" placeholder="Variation name"
                       class="form-control border-secondary">
                <input type="number" name="variations[${varIndex}][price]" placeholder="Price" step="0.01" min="0"
                       class="form-control border-secondary" style="max-width:140px">
                <button type="button" class="btn btn-outline-danger btn-remove-var"><i class="bi bi-x-lg"></i></button>
            </div>`);
        varIndex++;
    });

    $(document).on('click', '.btn-remove-var', function(){
        $(this).closest('.variation-row').remove();
    });
});
</script>
@endsection
