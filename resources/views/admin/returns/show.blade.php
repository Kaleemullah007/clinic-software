@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-arrow-return-left me-2 text-theme-color"></i>Return Details</h4>
            <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                <table class="table table-bordered table-sm">
                    <tr>
                        <th style="width:40%">Return ID</th>
                        <td>#{{ $return->id }}</td>
                    </tr>
                    <tr>
                        <th>Appointment</th>
                        <td>#{{ $return->appointment_id }}</td>
                    </tr>
                    <tr>
                        <th>Product</th>
                        <td>
                            {{ $return->product->name ?? ($return->appointmentProduct->product_name ?? '—') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Quantity Returned</th>
                        <td>{{ $return->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Refund Amount</th>
                        <td>PKR {{ number_format($return->refund_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Returned To</th>
                        <td>
                            <span class="badge {{ $return->return_to === 'inventory' ? 'bg-success' : 'bg-danger' }}">
                                {{ $return->return_to === 'inventory' ? 'Back to Inventory' : 'Marked Damaged' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Reason</th>
                        <td>{{ $return->reason ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Processed By</th>
                        <td>{{ $return->processedBy->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ $return->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>

                @can('returns.delete')
                <form action="{{ route('returns.destroy', $return) }}" method="POST"
                      class="mt-3" onsubmit="return confirm('Delete this return record?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> Delete Record
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .shadow-css { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
</style>
@endsection
