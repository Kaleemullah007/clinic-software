@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-receipt me-2 text-theme-color"></i>Appointment Receipt</h4>
            <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2 justify-content-center">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                <div class="text-center mb-3">
                    <h5 class="fw-bold">{{ config('app.name') }}</h5>
                    <p class="text-muted mb-0">{{ now()->format('d M Y') }}</p>
                </div>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <td>{{ $appointment->patient->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Appointment</th>
                            <td>{{ $appointment->appointment_id ?? $appointment->id }}</td>
                        </tr>
                    </thead>
                </table>
                <table class="table table-bordered mt-2">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($appointment->products as $p)
                        <tr>
                            <td>{{ $p->product_name }}{{ $p->variation ? ' ('.$p->variation->name.')' : '' }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>PKR {{ number_format($p->unit_price,2) }}</td>
                            <td>PKR {{ number_format($p->total_price,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td>PKR {{ number_format($total,2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-center mt-4 d-flex gap-3 justify-content-center">
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <a href="{{ $waUrl }}" target="_blank" class="btn btn-success">
                        <i class="bi bi-whatsapp me-1"></i> Send via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    @media print {
        .left-menu,.header,.btn,.breadcrumb { display:none !important; }
        .content-wrapper { margin:0 !important; }
        .shadow-css { box-shadow:none !important; }
    }
</style>
@endsection
