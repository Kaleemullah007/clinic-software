@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-bag-plus me-2 text-theme-color"></i>Appointment Products</h4>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="apTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>Appointment</th><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Doctor Share</th><th>Added By</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->appointment->appointment_id ?? $item->appointment_id }}</td>
                            <td>{{ $item->product_name }}{{ $item->variation ? ' ('.$item->variation->name.')' : '' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>PKR {{ number_format($item->unit_price,2) }}</td>
                            <td>PKR {{ number_format($item->total_price,2) }}</td>
                            <td>PKR {{ number_format($item->doctor_share_amount,2) }}</td>
                            <td>{{ $item->addedBy->name ?? '—' }}</td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>
                                @can('appointment-products.delete')
                                <form action="{{ route('appointment-products.destroy',$item) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>$(function(){ $('#apTable').DataTable({responsive:true,pageLength:25,order:[[7,'desc']],paging:false}); });</script>
@endsection
