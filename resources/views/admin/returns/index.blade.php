@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-arrow-return-left me-2 text-theme-color"></i>Returns</h4>
            @can('returns.create')
            <a href="{{ route('returns.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> Process Return</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="returnTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>#</th><th>Appointment</th><th>Product</th><th>Qty</th><th>Return To</th><th>Refund</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($returns as $i => $r)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $r->appointment->appointment_id ?? $r->appointment_id }}</td>
                            <td>{{ $r->product->name ?? '—' }}</td>
                            <td>{{ $r->quantity }}</td>
                            <td><span class="badge {{ $r->return_to==='inventory'?'bg-success':'bg-danger' }}">{{ ucfirst($r->return_to) }}</span></td>
                            <td>{{ $r->refund_amount ? 'PKR '.number_format($r->refund_amount,2) : '—' }}</td>
                            <td>{{ $r->created_at->format('d M Y') }}</td>
                            <td>
                                @can('returns.delete')
                                <form action="{{ route('returns.destroy',$r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
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
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>$(function(){ $('#returnTable').DataTable({responsive:true,pageLength:25,order:[[6,'desc']]}); });</script>
@endsection
