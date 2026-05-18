@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-cart-plus me-2 text-theme-color"></i>New Purchase Request</h4>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-9 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('purchase-requests.store') }}" id="prForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes / Justification</label>
                        <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <label class="form-label fw-semibold">Items <span class="text-danger">*</span></label>
                    <div id="itemsContainer">
                        <div class="item-row row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <select name="items[0][product_id]" class="form-select border-secondary product-select" required>
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-variations="{{ $p->variations->toJson() }}" data-has-var="{{ $p->has_variations ? 1 : 0 }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 var-wrap d-none">
                                <select name="items[0][variation_id]" class="form-select border-secondary variation-select"></select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]" class="form-control border-secondary" placeholder="Qty" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="items[0][notes]" class="form-control border-secondary" placeholder="Notes">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-theme mt-1 mb-3" id="addItemBtn">
                        <i class="bi bi-plus me-1"></i> Add Item
                    </button>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-send me-1"></i> Submit Request</button>
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
const productOptions = @json($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'has_variations'=>$p->has_variations,'variations'=>$p->variations]));
let rowIdx = 1;

function buildRow(idx){
    return `<div class="item-row row g-2 mb-2 align-items-end">
        <div class="col-md-4">
            <select name="items[${idx}][product_id]" class="form-select border-secondary product-select" required>
                <option value="">— Select Product —</option>
                ${productOptions.map(p=>`<option value="${p.id}" data-has-var="${p.has_variations?1:0}" data-variations='${JSON.stringify(p.variations)}'>${p.name}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-3 var-wrap d-none">
            <select name="items[${idx}][variation_id]" class="form-select border-secondary variation-select"></select>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${idx}][quantity]" class="form-control border-secondary" placeholder="Qty" step="0.01" min="0.01" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="items[${idx}][notes]" class="form-control border-secondary" placeholder="Notes">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>`;
}

$(function(){
    $('#addItemBtn').on('click', function(){
        $('#itemsContainer').append(buildRow(rowIdx++));
    });

    $(document).on('change', '.product-select', function(){
        const row    = $(this).closest('.item-row');
        const hasVar = $(this).find(':selected').data('has-var');
        const vars   = $(this).find(':selected').data('variations') || [];
        const varWrap = row.find('.var-wrap');
        const varSel  = row.find('.variation-select');

        if(hasVar && vars.length){
            varSel.empty().append('<option value="">— Variation —</option>');
            vars.forEach(v => varSel.append(`<option value="${v.id}">${v.name} (PKR ${parseFloat(v.price).toFixed(2)})</option>`));
            varWrap.removeClass('d-none');
        } else {
            varWrap.addClass('d-none');
            varSel.val('');
        }
    });

    $(document).on('click', '.btn-remove-item', function(){
        if($('.item-row').length > 1) $(this).closest('.item-row').remove();
    });
});
</script>
@endsection
