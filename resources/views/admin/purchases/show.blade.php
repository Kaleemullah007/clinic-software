@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-bag-check me-2 text-theme-color"></i>{{ $purchase->purchase_number }}</h4>
            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-8">
            <div class="shadow-css p-4">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Vendor:</strong> {{ $purchase->vendor->name ?? '—' }}</div>
                    <div class="col-md-4"><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</div>
                    <div class="col-md-4"><strong>PR:</strong> {{ $purchase->purchaseRequest->pr_number ?? '—' }}</div>
                </div>
                <table class="table table-bordered">
                    <thead class="table-light"><tr><th>#</th><th>Product</th><th>Variation</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th>Selling Price</th></tr></thead>
                    <tbody>
                        @foreach($purchase->items as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td>{{ $item->variation->name ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>PKR {{ number_format($item->unit_cost,2) }}</td>
                            <td>PKR {{ number_format($item->total_cost,2) }}</td>
                            <td>{{ $item->selling_price ? 'PKR '.number_format($item->selling_price,2) : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td colspan="5" class="text-end">Total</td><td>PKR {{ number_format($purchase->total_amount,2) }}</td><td></td></tr>
                        <tr><td colspan="5" class="text-end">Discount</td><td>PKR {{ number_format($purchase->discount,2) }}</td><td></td></tr>
                        <tr><td colspan="5" class="text-end text-theme-color">Net Amount</td><td class="text-theme-color">PKR {{ number_format($purchase->net_amount,2) }}</td><td></td></tr>
                    </tfoot>
                </table>
                @if($purchase->notes)
                <div class="alert alert-light border mt-2"><strong>Notes:</strong> {{ $purchase->notes }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
@endsection
