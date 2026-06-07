@extends('layouts.admin')

@section('content')
@php
    $monthNames = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $printMonth  = $month ? $monthNames[(int)$month] : 'All Months';
    $printClinic = 'All Clinics';
    $printDoctor = 'All Doctors';
    if (!empty($clinicId) && isset($clinics)) {
        $cl = $clinics->firstWhere('id', $clinicId);
        if ($cl) $printClinic = $cl->name;
    }
    if (!empty($doctorId) && isset($doctors)) {
        $dr = $doctors->firstWhere('id', $doctorId);
        if ($dr) $printDoctor = $dr->name;
    }
@endphp

<div class="container-fluid">

    {{-- Print-only header --}}
    <div class="print-header-only">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <strong style="font-size:18px;color:#B1083C;">RKTech</strong>
            <span style="font-size:15px;font-weight:600;color:#1a1a2e;">— Revenue Report</span>
        </div>
        <div style="font-size:12px;color:#555;margin-bottom:4px;">
            Year: <strong>{{ $year }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
            Month: <strong>{{ $printMonth }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
            Clinic: <strong>{{ $printClinic }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
            Doctor: <strong>{{ $printDoctor }}</strong>
        </div>
        <div style="font-size:11px;color:#888;">Printed on: {{ now()->format('d M Y, h:i A') }}</div>
        <hr style="margin:10px 0 16px;">
    </div>

    {{-- Page Header --}}
    <div class="row pt-3 mx-1 align-items-center no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-theme-color"></i>Revenue Report</h4>
            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Reports
                </a>
            </div>
        </div>
        <hr class="my-3">
    </div>

    {{-- Filter Bar + Total Revenue --}}
    <div class="row mx-1 mb-4 align-items-center no-print">
        <div class="col-lg-8">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                <select name="year" class="form-select border-secondary form-select-sm" style="width:100px">
                    @for($y=now()->year;$y>=now()->year-3;$y--)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="form-select border-secondary form-select-sm" style="width:130px">
                    <option value="">All Months</option>
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                    @endfor
                </select>
                @if(auth()->user()->isSuperAdmin())
                <select name="clinic_id" class="form-select border-secondary form-select-sm" style="width:140px">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $c)
                    <option value="{{ $c->id }}" {{ ($clinicId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="doctor_id" class="form-select border-secondary form-select-sm" style="width:140px">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ ($doctorId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                @endif
                <button class="btn btn-theme btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            </form>
        </div>
        <div class="col-lg-4 d-flex justify-content-end align-items-center mt-3 mt-lg-0">
            <div class="shadow-css px-4 py-3 text-end" style="border-left:4px solid #B1083C;min-width:200px">
                <div class="text-muted small mb-1" style="font-size:.75rem;letter-spacing:.04em;text-transform:uppercase">Total Revenue</div>
                <div class="fw-bold text-theme-color" style="font-size:1.35rem">PKR {{ number_format($totalRevenue,2) }}</div>
            </div>
        </div>
    </div>

    <div class="row mx-1 mb-4">
        <div class="col-lg-8">
            <div class="shadow-css p-3">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Period</th><th>Revenue</th><th>Doctor Share</th><th>Clinic Share</th></tr>
                    </thead>
                    <tbody>
                        @foreach($query as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::create($row->year,$row->month)->format('F Y') }}</td>
                            <td class="fw-semibold text-theme-color">PKR {{ number_format($row->revenue,2) }}</td>
                            <td>PKR {{ number_format($row->doctor_share,2) }}</td>
                            <td>PKR {{ number_format($row->clinic_share,2) }}</td>
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
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = @json($query->map(fn($r)=>\Carbon\Carbon::create($r->year,$r->month)->format('M Y')));
const revenues = @json($query->pluck('revenue'));
const doctorShares = @json($query->pluck('doctor_share'));
const clinicShares = @json($query->pluck('clinic_share'));

new Chart(document.getElementById('revenueChart'),{
    type:'bar',
    data:{
        labels,
        datasets:[
            {label:'Revenue',data:revenues,backgroundColor:'rgba(177,8,60,0.7)',borderRadius:4},
            {label:'Doctor Share',data:doctorShares,backgroundColor:'rgba(54,162,235,0.6)',borderRadius:4},
            {label:'Clinic Share',data:clinicShares,backgroundColor:'rgba(75,192,192,0.6)',borderRadius:4},
        ]
    },
    options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}
});
</script>
@endsection
