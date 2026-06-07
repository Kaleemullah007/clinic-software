@extends('layouts.admin')
@section('title', 'Service Gap Report')

@section('content')
@include('admin.reports._styles')
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
<style>
    .gap-badge-active  { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; }
    .gap-badge-dormant { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; }
    .gap-badge-never   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; }
    .gap-unused-row    { background:#fff8f8; }
    .print-header-only { display: none; }
    @media print {
        canvas { max-height: 280px !important; }
        .rpt-panel { box-shadow: none !important; border: 1px solid #e5e5e5; }
        .rpt-stat-card { box-shadow: none !important; border: 1px solid #ddd; break-inside: avoid; }
        .rpt-table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="container-fluid">

    {{-- Print-only header --}}
    <div class="print-header-only">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <strong style="font-size:18px;color:#B1083C;">RKTech</strong>
            <span style="font-size:15px;font-weight:600;color:#1a1a2e;">— Service Gap Report</span>
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

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="row pt-3 mx-1 align-items-center no-print">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-search-heart me-2" style="color:#B1083C"></i>Service Gap Report
            </h4>
        </div>
        <hr class="my-3">
    </div>

    {{-- ── Filter Bar ──────────────────────────────────────────────────── --}}
    <div class="row mx-1 mb-4 g-2 align-items-end no-print">
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Year</label>
            <select id="filterYear" class="form-select form-select-sm border-secondary" style="width:110px">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Month</label>
            <select id="filterMonth" class="form-select form-select-sm border-secondary" style="width:130px">
                <option value="">All Months</option>
                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $mn)
                    <option value="{{ $i+1 }}" {{ $month == ($i+1) ? 'selected' : '' }}>{{ $mn }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Clinic</label>
            <select id="filterClinic" class="form-select form-select-sm border-secondary" style="width:160px">
                <option value="">All Clinics</option>
                @foreach($clinics as $c)
                <option value="{{ $c->id }}" {{ ($clinicId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Doctor</label>
            <select id="filterDoctor" class="form-select form-select-sm border-secondary" style="width:160px">
                <option value="">All Doctors</option>
                @foreach($doctors as $d)
                <option value="{{ $d->id }}" {{ ($doctorId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button id="applyFilter" class="btn btn-sm btn-danger">
                <i class="bi bi-funnel me-1"></i>Apply
            </button>
        </div>
        <div class="col-auto">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────────── --}}
    <div class="row mx-1 g-3 mb-4">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="bi bi-grid-3x3-gap"></i></div>
                <div class="rpt-stat-num">{{ $totalServices }}</div>
                <div class="rpt-stat-lbl">Total Services in System</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-check-circle"></i></div>
                <div class="rpt-stat-num">{{ $activeCount }}</div>
                <div class="rpt-stat-lbl">Active This Period</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-x-circle"></i></div>
                <div class="rpt-stat-num">{{ $unusedCount }}</div>
                <div class="rpt-stat-lbl">Unused This Period</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-currency-dollar"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($totalRevenue, 0) }}</div>
                <div class="rpt-stat-lbl">Revenue from Active Services</div>
            </div>
        </div>
    </div>

    {{-- ── Charts ───────────────────────────────────────────────────────── --}}
    <div class="row mx-1 g-4 mb-4">
        <div class="col-lg-7 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head"><i class="bi bi-bar-chart-horizontal me-2" style="color:#B1083C"></i>Top Services by Bookings</div>
                <div class="rpt-panel-body">
                    @if($topServices->isEmpty())
                        <p class="text-muted text-center py-4">No bookings for this period.</p>
                    @else
                        <canvas id="barChart" height="90"></canvas>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head"><i class="bi bi-pie-chart me-2" style="color:#B1083C"></i>Revenue Share by Service</div>
                <div class="rpt-panel-body d-flex align-items-center justify-content-center">
                    @if($topServices->isEmpty())
                        <p class="text-muted text-center py-4">No data.</p>
                    @else
                        <canvas id="donutChart" height="200" style="max-width:280px"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Top Services Table ───────────────────────────────────────────── --}}
    <div class="row mx-1 g-4 mb-4">
        <div class="col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">
                    <i class="bi bi-trophy me-2" style="color:#f59e0b"></i>Active Services — Ranked by Bookings
                    <span class="badge ms-2" style="background:#B1083C">{{ $year }}{{ $month ? ' / ' . str_pad($month,2,'0',STR_PAD_LEFT) : '' }}</span>
                </div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Discount Given</th>
                                <th class="text-end">Avg / Booking</th>
                                <th class="text-center">Share</th>
                                <th>Last Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topServices as $i => $row)
                            @php
                                $share = $totalRevenue > 0 ? round($row->revenue / $totalRevenue * 100, 1) : 0;
                                $avg   = $row->booking_count > 0 ? $row->revenue / $row->booking_count : 0;
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row->service_name }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $row->booking_count }}</span></td>
                                <td class="text-end fw-semibold" style="color:#B1083C">PKR {{ number_format($row->revenue, 0) }}</td>
                                <td class="text-end" style="color:#f59e0b">– PKR {{ number_format($row->total_discount, 0) }}</td>
                                <td class="text-end text-muted small">PKR {{ number_format($avg, 0) }}</td>
                                <td class="text-center" style="min-width:120px">
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="flex-grow-1 bg-light rounded" style="height:6px">
                                            <div class="rounded" style="height:6px;width:{{ $share }}%;background:#B1083C"></div>
                                        </div>
                                        <small class="text-muted" style="min-width:34px">{{ $share }}%</small>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $row->last_booked ? \Carbon\Carbon::parse($row->last_booked)->format('d M Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No bookings for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Unused / Dead Services Table ────────────────────────────────── --}}
    <div class="row mx-1 g-4 mb-5">
        <div class="col-12">
            <div class="rpt-panel" style="border:2px solid #fee2e2">
                <div class="rpt-panel-head" style="background:#fff5f5">
                    <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                    Unused Services — Not booked in this period
                    <span class="badge bg-danger ms-2">{{ $unusedCount }}</span>
                </div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th class="text-end">Listed Price</th>
                                <th>Status in System</th>
                                <th>Last Ever Booked</th>
                                <th class="text-center">Diagnosis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unusedServices as $i => $svc)
                            @php
                                $lastEver = $everBooked[$svc->id]->last_booked_ever ?? null;
                                $daysSince = $lastEver ? \Carbon\Carbon::parse($lastEver)->diffInDays(now()) : null;
                                if (!$lastEver) {
                                    $badge = '<span class="gap-badge-never">Never Booked</span>';
                                } elseif ($daysSince > 180) {
                                    $badge = '<span class="gap-badge-dormant">Dormant (' . $daysSince . 'd)</span>';
                                } else {
                                    $badge = '<span class="gap-badge-dormant">Not this period</span>';
                                }
                            @endphp
                            <tr class="gap-unused-row">
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold text-danger">{{ $svc->name }}</td>
                                <td class="text-end text-muted">{{ $svc->price ? 'PKR ' . number_format($svc->price, 0) : '—' }}</td>
                                <td>
                                    @if($svc->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if($lastEver)
                                        {{ \Carbon\Carbon::parse($lastEver)->format('d M Y') }}
                                        <span class="text-muted">({{ $daysSince }}d ago)</span>
                                    @else
                                        <span class="text-danger fw-semibold">Never</span>
                                    @endif
                                </td>
                                <td class="text-center">{!! $badge !!}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4" style="color:#10b981">
                                    <i class="bi bi-check-circle-fill me-2"></i>All services were booked this period!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const COLORS = [
    '#B1083C','#d13729','#e05a45','#f97316','#f59e0b',
    '#84cc16','#10b981','#06b6d4','#6366f1','#a855f7',
    '#ec4899','#14b8a6','#64748b','#f43f5e','#0ea5e9'
];

@if($topServices->isNotEmpty())
@php
    $barColors = $topServices->keys()->map(
        fn($i) => 'rgba(177,8,60,' . number_format(max(0.35, 0.9 - $i * 0.05), 2) . ')'
    )->values()->all();
@endphp
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels:   @json($topServices->pluck('service_name')),
        datasets: [{
            label: 'Bookings',
            data:  @json($topServices->pluck('booking_count')),
            backgroundColor: @json($barColors),
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels:   @json($topServices->pluck('service_name')),
        datasets: [{
            data:            @json($topServices->pluck('revenue')),
            backgroundColor: COLORS.slice(0, {{ $topServices->count() }}),
            borderWidth: 2,
        }]
    },
    options: {
        cutout: '60%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ' PKR ' + ctx.parsed.toLocaleString() } }
        }
    }
});
@endif

document.getElementById('applyFilter').addEventListener('click', function () {
    const y = document.getElementById('filterYear').value;
    const m = document.getElementById('filterMonth').value;
    const c = document.getElementById('filterClinic').value;
    const d = document.getElementById('filterDoctor').value;
    let url = '{{ route("reports.service-gap") }}?year=' + y;
    if (m) url += '&month=' + m;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    window.location.href = url;
});
</script>
@endsection
