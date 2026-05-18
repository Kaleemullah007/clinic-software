@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-exclamation-triangle me-2 text-theme-color"></i>Report Damaged Product</h4>
            <a href="{{ route('damaged-products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('damaged-products.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select border-secondary" id="dmgProduct" required>
                                <option value="">— Select —</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" data-has-var="{{ $p->has_variations?1:0 }}" data-variations="{{ $p->variations->toJson() }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-none" id="dmgVarWrap">
                            <label class="form-label fw-semibold">Variation</label>
                            <select name="variation_id" class="form-select border-secondary" id="dmgVariation"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control border-secondary" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cost Value (PKR)</label>
                            <input type="number" name="cost_value" class="form-control border-secondary" step="0.01" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reason</label>
                            <textarea name="reason" class="form-control border-secondary" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Submit</button>
                        <a href="{{ route('damaged-products.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>
$(function(){
    $('#dmgProduct').on('change', function(){
        const hasVar=$(this).find(':selected').data('has-var');
        const vars=$(this).find(':selected').data('variations')||[];
        const wrap=$('#dmgVarWrap'); const sel=$('#dmgVariation');
        if(hasVar&&vars.length){ sel.empty().append('<option value="">—</option>'); vars.forEach(v=>sel.append(`<option value="${v.id}">${v.name}</option>`)); wrap.removeClass('d-none'); }
        else { wrap.addClass('d-none'); sel.val(''); }
    });
});
</script>
@endsection
