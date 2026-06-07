@extends('layouts.admin')

@section('content')
@php
    $monthNames = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $printMonth  = $month ? $monthNames[(int)$month] : 'All Months';
    $printClinic = 'All Clinics';
    if (!empty($clinicId) && isset($clinics)) {
        $cl = $clinics->firstWhere('id', $clinicId);
        if ($cl) $printClinic = $cl->name;
    }
@endphp

<div class="container-fluid">

    {{-- Print-only header --}}
    <div class="print-header-only">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <strong style="font-size:18px;color:#B1083C;">RKTech</strong>
            <span style="font-size:15px;font-weight:600;color:#1a1a2e;">— Expense Report</span>
        </div>
        <div style="font-size:12px;color:#555;margin-bottom:4px;">
            Year: <strong>{{ $year }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
            Month: <strong>{{ $printMonth }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
            Clinic: <strong>{{ $printClinic }}</strong>
        </div>
        <div style="font-size:11px;color:#888;">Printed on: {{ now()->format('d M Y, h:i A') }}</div>
        <hr style="margin:10px 0 16px;">
    </div>

    <div class="row pt-3 mx-1 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-cash-stack me-2 text-theme-color"></i>Expense Report</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1 mb-3 no-print">
        <div class="col-12">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label small text-muted mb-1">Year</label>
                    <select name="year" class="form-select border-secondary form-select-sm" style="width:100px">
                        @for($y=now()->year;$y>=now()->year-3;$y--)
                        <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="form-label small text-muted mb-1">Month</label>
                    <select name="month" class="form-select border-secondary form-select-sm" style="width:130px">
                        <option value="">All Months</option>
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="form-label small text-muted mb-1">Clinic</label>
                    <select name="clinic_id" class="form-select border-secondary form-select-sm" style="width:160px">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ ($clinicId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="align-self-end">
                    <button class="btn btn-theme btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
                <div class="align-self-end">
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Grand Total Card --}}
    <div class="row mx-1 mb-3">
        <div class="col-lg-4 col-md-6 col-12">
            <div class="shadow-css p-3 d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(177,8,60,.1);display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-cash-stack" style="color:#B1083C;font-size:1.2rem"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.15rem;color:#B1083C">PKR {{ number_format($grandTotal, 0) }}</div>
                    <div class="small text-muted">Total Expenses{{ ($clinicId ?? '') ? ' — ' . ($clinics->firstWhere('id', $clinicId)?->name ?? '') : '' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mx-1 mb-4">
        <div class="col-lg-5">
            <div class="shadow-css p-3"><canvas id="pieChart" height="220"></canvas></div>
        </div>
        <div class="col-lg-7">
            <div class="shadow-css p-3"><canvas id="monthlyChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="row mx-1">
        <div class="col-md-6">
            <div class="shadow-css p-3">
                <h6 class="fw-bold mb-3">By Category</h6>
                <table class="table table-sm table-hover">
                    <thead class="table-light"><tr><th>Category</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->category ?? 'Uncategorised' }}</td>
                            <td class="fw-semibold">PKR {{ number_format($row->total,2) }}</td>
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
.print-header-only { display: none; }
@media print {
    canvas { max-height: 280px !important; }
    .shadow-css { box-shadow: none !important; border: 1px solid #eee; }
    .table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@php
    $monthLabelArr = [];
    $monthValArr   = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthLabelArr[] = \Carbon\Carbon::create(null, $m)->format('M');
        $monthValArr[]   = $monthly[$m]->total ?? 0;
    }
@endphp
<script>
const catLabels   = @json($data->pluck('category')->map(fn($c) => $c ?? 'Uncategorised'));
const catTotals   = @json($data->pluck('total'));
const monthLabels = @json($monthLabelArr);
const monthVals   = @json($monthValArr);

new Chart(document.getElementById('pieChart'),{type:'pie',data:{labels:catLabels,datasets:[{data:catTotals,backgroundColor:['#B1083C','#d13729','#f76c6c','#ffa07a','#ffd700','#98fb98','#87ceeb']}]},options:{responsive:true,plugins:{legend:{position:'right'}}}});
new Chart(document.getElementById('monthlyChart'),{type:'line',data:{labels:monthLabels,datasets:[{label:'Monthly Expenses',data:monthVals,borderColor:'#B1083C',backgroundColor:'rgba(177,8,60,0.1)',fill:true,tension:0.4}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}});
</script>
@endsection
