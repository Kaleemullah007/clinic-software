@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-bag-plus me-2 text-theme-color"></i>New Purchase Order</h4>
            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('purchases.store') }}" id="poForm">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Vendor</label>
                            <select name="vendor_id" class="form-select border-secondary">
                                <option value="">— Select —</option>
                                @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}{{ $v->company ? ' ('.$v->company.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Approved PR (optional)</label>
                            <select name="purchase_request_id" class="form-select border-secondary">
                                <option value="">—</option>
                                @foreach($pendingPRs as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->pr_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control border-secondary" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select border-secondary" required>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Paid Amount</label>
                            <input type="number" name="paid_amount" class="form-control border-secondary" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Discount</label>
                            <input type="number" name="discount" class="form-control border-secondary" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control border-secondary">
                        </div>
                    </div>

                    <label class="form-label fw-semibold">Items <span class="text-danger">*</span></label>
                    <div id="itemsContainer">
                        <div class="item-row row g-2 mb-2 align-items-end">
                            <div class="col-md-3">
                                <select name="items[0][product_id]" class="form-select border-secondary product-select" required>
                                    <option value="">— Product —</option>
                                    @foreach($productOpts as $p)
                                    <option value="{{ $p['id'] }}" data-has-var="{{ $p['has_variations']?1:0 }}" data-price="{{ $p['price'] }}" data-variations="{{ json_encode($p['variations']) }}">{{ $p['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 var-wrap d-none">
                                <select name="items[0][variation_id]" class="form-select border-secondary variation-select"></select>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="items[0][quantity]" class="form-control border-secondary" placeholder="Qty" step="0.01" min="0.01" required value="1">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][unit_cost]" class="form-control border-secondary" placeholder="Cost/Unit" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][selling_price]" class="form-control border-secondary" placeholder="Selling Price" step="0.01" min="0">
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
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save Purchase</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.btn-outline-theme{border-color:#B1083C;color:#B1083C;}.btn-outline-theme:hover{background:#B1083C;color:#fff;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>
const productOpts = @json($productOpts);
let idx = 1;

function buildRow(i){
    return `<div class="item-row row g-2 mb-2 align-items-end">
        <div class="col-md-3"><select name="items[${i}][product_id]" class="form-select border-secondary product-select" required>
            <option value="">— Product —</option>
            ${productOpts.map(p=>`<option value="${p.id}" data-has-var="${p.has_variations?1:0}" data-price="${p.price||0}" data-variations='${JSON.stringify(p.variations)}'>${p.name}</option>`).join('')}
        </select></div>
        <div class="col-md-2 var-wrap d-none"><select name="items[${i}][variation_id]" class="form-select border-secondary variation-select"></select></div>
        <div class="col-md-1"><input type="number" name="items[${i}][quantity]" class="form-control border-secondary" placeholder="Qty" step="0.01" min="0.01" required value="1"></div>
        <div class="col-md-2"><input type="number" name="items[${i}][unit_cost]" class="form-control border-secondary" placeholder="Cost/Unit" step="0.01" min="0" required></div>
        <div class="col-md-2"><input type="number" name="items[${i}][selling_price]" class="form-control border-secondary" placeholder="Selling Price" step="0.01" min="0"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button></div>
    </div>`;
}

$(function(){
    $('#addItemBtn').on('click', ()=>{ $('#itemsContainer').append(buildRow(idx++)); });

    $(document).on('change', '.product-select', function(){
        const row=$(this).closest('.item-row');
        const hasVar=$(this).find(':selected').data('has-var');
        const vars=$(this).find(':selected').data('variations')||[];
        const price=$(this).find(':selected').data('price')||0;
        const varWrap=row.find('.var-wrap'); const varSel=row.find('.variation-select');
        if(hasVar&&vars.length){ varSel.empty().append('<option value="">—</option>'); vars.forEach(v=>varSel.append(`<option value="${v.id}" data-price="${v.price}">${v.name} (PKR ${parseFloat(v.price).toFixed(2)})</option>`)); varWrap.removeClass('d-none'); }
        else { varWrap.addClass('d-none'); row.find('[name$="[unit_cost]"]').val(price||''); }
    });

    $(document).on('change', '.variation-select', function(){
        const price=$(this).find(':selected').data('price')||0;
        $(this).closest('.item-row').find('[name$="[unit_cost]"]').val(price);
    });

    $(document).on('click', '.btn-remove-item', function(){ if($('.item-row').length>1) $(this).closest('.item-row').remove(); });
});
</script>
@endsection
