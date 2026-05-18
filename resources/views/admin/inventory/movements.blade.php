@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-clock-history me-2 text-theme-color"></i>Stock Movements</h4>
            <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table id="movTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Product</th><th>Variation</th><th>Type</th><th>Qty</th><th>Unit Cost</th><th>By</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $m)
                        <tr>
                            <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $m->product->name ?? '—' }}</td>
                            <td>{{ $m->variation->name ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$m->type)) }}</span></td>
                            <td>
                                <span class="{{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $m->quantity >= 0 ? '+' : '' }}{{ $m->quantity }}
                                </span>
                            </td>
                            <td>{{ $m->unit_price ? 'PKR '.number_format($m->unit_price,2) : '—' }}</td>
                            <td>{{ $m->creator->name ?? '—' }}</td>
                            <td>{{ $m->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>
$(function(){ $('#movTable').DataTable({responsive:true,pageLength:50,order:[[0,'desc']],paging:false}); });
</script>
@endsection
