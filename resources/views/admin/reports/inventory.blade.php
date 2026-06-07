@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Print-only header --}}
    <div class="print-header-only">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <strong style="font-size:18px;color:#B1083C;">RKTech</strong>
            <span style="font-size:15px;font-weight:600;color:#1a1a2e;">— Inventory Report</span>
        </div>
        <div style="font-size:11px;color:#888;">Printed on: {{ now()->format('d M Y, h:i A') }}</div>
        <hr style="margin:10px 0 16px;">
    </div>

    <div class="row pt-3 mx-1 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-boxes me-2 text-theme-color"></i>Inventory Report</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Reports</a>
            </div>
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1 mb-3 g-3">
        <div class="col-md-4">
            <div class="shadow-css p-3 text-center">
                <p class="text-muted small mb-1">Total Stock Value</p>
                <h4 class="fw-bold text-theme-color">PKR {{ number_format($totalValue,2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="shadow-css p-3 text-center">
                <p class="text-muted small mb-1">Low Stock Items (≤5)</p>
                <h4 class="fw-bold text-danger">{{ $lowStock->count() }}</h4>
            </div>
        </div>
    </div>

    @if($lowStock->count())
    <div class="row mx-1 mb-4">
        <div class="col-12">
            <div class="shadow-css p-3">
                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alert</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light"><tr><th>Product</th><th>Variation</th><th>Stock</th><th>Cost Price</th></tr></thead>
                    <tbody>
                        @foreach($lowStock as $item)
                        <tr class="{{ $item->quantity <= 0 ? 'table-danger' : 'table-warning' }}">
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td>{{ $item->variation->name ?? '—' }}</td>
                            <td class="fw-bold">{{ $item->quantity }}</td>
                            <td>PKR {{ number_format($item->cost_price,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <h6 class="fw-bold mb-3">All Stock</h6>
                <table id="stockTable" class="table table-hover w-100">
                    <thead class="table-light"><tr><th>Product</th><th>Variation</th><th>Stock</th><th>Cost Price</th><th>Value</th></tr></thead>
                    <tbody>
                        @foreach($allStock as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td>{{ $item->variation->name ?? '—' }}</td>
                            <td><span class="badge {{ $item->quantity > 5 ? 'bg-success' : ($item->quantity > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $item->quantity }}</span></td>
                            <td>PKR {{ number_format($item->cost_price,2) }}</td>
                            <td>PKR {{ number_format($item->quantity * $item->cost_price,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
.text-theme-color{color:#B1083C;}
.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.print-header-only { display: none; }
@media print {
    .shadow-css { box-shadow: none !important; border: 1px solid #eee; }
    .table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
<script>$(function(){ $('#stockTable').DataTable({responsive:true,pageLength:25}); });</script>
@endsection
