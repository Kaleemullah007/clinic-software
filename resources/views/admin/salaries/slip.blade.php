@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-receipt me-2 text-theme-color"></i>Salary Slip</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Print</button>
                <a href="{{ route('salaries.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 justify-content-center">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-5" id="slipContent">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">{{ config('app.name') }}</h4>
                    <h5 class="text-muted">Salary Slip — {{ \Carbon\Carbon::create($salary->year,$salary->month)->format('F Y') }}</h5>
                    <hr>
                </div>
                <table class="table table-sm">
                    <tr><td class="fw-semibold" width="45%">Employee</td><td>{{ $salary->user->name }}</td></tr>
                    <tr><td class="fw-semibold">CNIC</td><td>{{ $salary->user->cnic ?? '—' }}</td></tr>
                    <tr><td class="fw-semibold">Joining Date</td><td>{{ $salary->user->joining_date ? \Carbon\Carbon::parse($salary->user->joining_date)->format('d M Y') : '—' }}</td></tr>
                    <tr><td class="fw-semibold">Salary Type</td><td>{{ ucfirst($salary->user->salary_type) }}</td></tr>
                    <tr><td class="fw-semibold">Bank Account</td><td>{{ $salary->user->bank_account ?? '—' }}</td></tr>
                </table>
                <hr>
                <table class="table table-sm table-bordered">
                    <thead class="table-light"><tr><th colspan="2">Earnings</th><th colspan="2">Deductions</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td><td>PKR {{ number_format($salary->basic_salary,2) }}</td>
                            <td>Deductions</td><td>PKR {{ number_format($salary->deductions,2) }}</td>
                        </tr>
                        <tr>
                            <td>Bonus</td><td>PKR {{ number_format($salary->bonus,2) }}</td>
                            <td></td><td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2">Gross: PKR {{ number_format($salary->basic_salary + $salary->bonus,2) }}</td>
                            <td colspan="2">Net: <span class="text-theme-color">PKR {{ number_format($salary->net_salary,2) }}</span></td>
                        </tr>
                    </tfoot>
                </table>
                @if($salary->notes)
                <p class="text-muted mt-2"><small>{{ $salary->notes }}</small></p>
                @endif
                <div class="d-flex justify-content-between mt-5">
                    <div class="text-center"><hr style="width:120px"><small>Employee Signature</small></div>
                    <div class="text-center"><hr style="width:120px"><small>Authorised By</small></div>
                </div>
                <p class="text-center text-muted mt-3"><small>Generated on {{ now()->format('d M Y H:i') }}</small></p>
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
        .left-menu,.header,.btn,.d-flex.justify-content-between.align-items-center,.breadcrumb{display:none!important;}
        .content-wrapper{margin:0!important;}
        .shadow-css{box-shadow:none!important;}
        hr.my-2{display:none!important;}
    }
</style>
@endsection
