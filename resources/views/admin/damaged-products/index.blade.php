@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-exclamation-triangle me-2 text-theme-color"></i>Damaged Products</h4>
            @can('damaged-products.create')
            <a href="{{ route('damaged-products.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> Report Damage</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="dmgTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>#</th><th>Product</th><th>Variation</th><th>Qty</th><th>Cost Value</th><th>Reason</th><th>Reported By</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($records as $i => $r)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $r->product->name ?? '—' }}</td>
                            <td>{{ $r->variation->name ?? '—' }}</td>
                            <td>{{ $r->quantity }}</td>
                            <td>{{ $r->cost_value ? 'PKR '.number_format($r->cost_value,2) : '—' }}</td>
                            <td>{{ Str::limit($r->reason,40) ?? '—' }}</td>
                            <td>{{ $r->reportedBy->name ?? '—' }}</td>
                            <td>{{ $r->created_at->format('d M Y') }}</td>
                            <td>
                                @can('damaged-products.delete')
                                <form action="{{ route('damaged-products.destroy',$r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
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
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>$(function(){ $('#dmgTable').DataTable({responsive:true,pageLength:25,order:[[7,'desc']]}); });</script>
@endsection
