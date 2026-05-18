@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-boxes me-2 text-theme-color"></i>Inventory</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('inventory.movements') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-clock-history me-1"></i> Movements
                </a>
                @can('inventory.create')
                <button class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#adjustModal">
                    <i class="bi bi-arrow-left-right me-1"></i> Adjust Stock
                </button>
                @endcan
            </div>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="invTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Variation</th><th>Stock</th><th>Cost Price</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            @if($p->has_variations)
                                @foreach($p->variations as $var)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $var->name }}</span></td>
                                    <td>
                                        <span class="badge fs-6 {{ ($var->inventory->quantity ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $var->inventory->quantity ?? 0 }}
                                        </span>
                                    </td>
                                    <td>PKR {{ number_format($var->inventory->cost_price ?? 0, 2) }}</td>
                                    <td><a href="{{ route('inventory.show', $p) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td>—</td>
                                <td>
                                    <span class="badge fs-6 {{ ($p->inventory->quantity ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $p->inventory->quantity ?? 0 }}
                                    </span>
                                </td>
                                <td>PKR {{ number_format($p->inventory->cost_price ?? 0, 2) }}</td>
                                <td><a href="{{ route('inventory.show', $p) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@can('inventory.create')
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2 text-theme-color"></i>Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('inventory.adjust') }}">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select border-secondary" id="adjProduct" required>
                            <option value="">— Select —</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" data-variations="{{ $p->variations->toJson() }}" data-has-var="{{ $p->has_variations ? 1 : 0 }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-none" id="adjVarWrap">
                        <label class="form-label fw-semibold">Variation</label>
                        <select name="variation_id" class="form-select border-secondary" id="adjVariation"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control border-secondary" step="0.01" required
                               placeholder="Use negative to remove stock">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unit Cost</label>
                        <input type="number" name="unit_price" class="form-control border-secondary" step="0.01" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select border-secondary" required>
                            <option value="purchase">Purchase</option>
                            <option value="adjustment">Manual Adjustment</option>
                            <option value="return">Return</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control border-secondary" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>
$(function(){
    $('#invTable').DataTable({responsive:true,pageLength:25});

    $('#adjProduct').on('change', function(){
        const hasVar = $(this).find(':selected').data('has-var');
        const vars   = $(this).find(':selected').data('variations') || [];
        const wrap   = $('#adjVarWrap');
        const sel    = $('#adjVariation');
        if(hasVar && vars.length){
            sel.empty().append('<option value="">— Select Variation —</option>');
            vars.forEach(v => sel.append(`<option value="${v.id}">${v.name}</option>`));
            wrap.removeClass('d-none');
        } else { wrap.addClass('d-none'); sel.val(''); }
    });
});
</script>
@endsection
