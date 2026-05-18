@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-pencil-square me-2 text-theme-color"></i>Edit Product</h4>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1 mt-2">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('products.update', $product) }}" id="productForm">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select border-secondary">
                                <option value="1" {{ old('status',$product->status)==1?'selected':'' }}>Active</option>
                                <option value="0" {{ old('status',$product->status)==0?'selected':'' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control border-secondary" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="track_inventory" value="1"
                                       id="trackInventory" {{ $product->track_inventory ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="trackInventory">Track Inventory</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="basePriceWrap" {{ $product->has_variations ? 'style=display:none' : '' }}>
                            <label class="form-label fw-semibold">Base Price (PKR)</label>
                            <input type="number" name="price" step="0.01" min="0"
                                   class="form-control border-secondary"
                                   value="{{ old('price', $product->price) }}">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Update</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Variations Panel (separate card) --}}
        @if($product->has_variations)
        <div class="col-lg-4 col-12 mt-3 mt-lg-0">
            <div class="shadow-css p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-2 text-theme-color"></i>Variations</h6>
                <div id="variationsList">
                    @foreach($product->variations as $var)
                    <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 var-item" data-id="{{ $var->id }}">
                        <div>
                            <strong>{{ $var->name }}</strong>
                            <small class="text-muted ms-2">PKR {{ number_format($var->price,2) }}</small>
                        </div>
                        @can('products.edit')
                        <button class="btn btn-sm btn-outline-danger btn-delete-var" data-id="{{ $var->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endcan
                    </div>
                    @endforeach
                </div>

                @can('products.edit')
                <hr>
                <div class="d-flex gap-2 mt-2">
                    <input type="text" id="newVarName" class="form-control border-secondary" placeholder="Name">
                    <input type="number" id="newVarPrice" class="form-control border-secondary" placeholder="Price" style="max-width:110px" step="0.01" min="0">
                    <button class="btn btn-theme" id="addVarBtn"><i class="bi bi-plus-lg"></i></button>
                </div>
                @endcan
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>
$(function(){
    $('#addVarBtn').on('click', function(){
        const name  = $('#newVarName').val().trim();
        const price = $('#newVarPrice').val().trim();
        if(!name || !price){ alert('Name and price required.'); return; }

        $.ajax({
            url: '{{ route("product.variation.store", $product) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', name, price },
            success: function(res){
                if(res.success){
                    $('#variationsList').append(`
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 var-item" data-id="${res.variation.id}">
                            <div><strong>${res.variation.name}</strong> <small class="text-muted ms-2">PKR ${parseFloat(res.variation.price).toFixed(2)}</small></div>
                            <button class="btn btn-sm btn-outline-danger btn-delete-var" data-id="${res.variation.id}"><i class="bi bi-trash"></i></button>
                        </div>`);
                    $('#newVarName').val('');
                    $('#newVarPrice').val('');
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-var', function(){
        if(!confirm('Delete this variation?')) return;
        const id = $(this).data('id');
        const row = $(this).closest('.var-item');
        $.ajax({
            url: '{{ url("products/variations") }}/' + id,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
            success: function(res){ if(res.success) row.remove(); }
        });
    });
});
</script>
@endsection
