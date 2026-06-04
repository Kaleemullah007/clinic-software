@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1 align-items-center">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar2-check me-2" style="color:#B1083C"></i>Appointment Report</h4>
        </div>
        <hr class="my-3">
    </div>

    {{-- Filter Bar --}}
    <div class="row mx-1 mb-4 g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Year</label>
            <select id="filterYear" class="form-select form-select-sm border-secondary" style="width:110px">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Clinic</label>
            <select id="filterClinic" class="form-select form-select-sm border-secondary" style="width:150px">
                <option value="">All Clinics</option>
                @foreach($clinics as $c)
                <option value="{{ $c->id }}" {{ ($clinicId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Doctor</label>
            <select id="filterDoctor" class="form-select form-select-sm border-secondary" style="width:150px">
                <option value="">All Doctors</option>
                @foreach($doctors as $d)
                <option value="{{ $d->id }}" {{ ($doctorId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-auto">
            <button id="applyFilter" class="btn btn-sm btn-danger">
                <i class="bi bi-funnel me-1"></i>Apply
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mx-1 g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-calendar2-check"></i></div>
                <div class="rpt-stat-num">{{ number_format($totalAppts) }}</div>
                <div class="rpt-stat-lbl">Total Appointments</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-check-circle"></i></div>
                <div class="rpt-stat-num">{{ number_format($paidCount) }}</div>
                <div class="rpt-stat-lbl">Paid</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="bi bi-hourglass-split"></i></div>
                <div class="rpt-stat-num">{{ number_format($unpaidCount) }}</div>
                <div class="rpt-stat-lbl">Unpaid</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9"><i class="bi bi-cash-coin"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($collected, 0) }}</div>
                <div class="rpt-stat-lbl">Collected</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-exclamation-circle"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($remaining, 0) }}</div>
                <div class="rpt-stat-lbl">Outstanding</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="bi bi-people"></i></div>
                <div class="rpt-stat-num">{{ number_format($newCount) }} / {{ number_format($returningCount) }}</div>
                <div class="rpt-stat-lbl">New / Returning</div>
            </div>
        </div>
    </div>

    <div class="row mx-1 g-4">
        {{-- Monthly Volume Chart --}}
        <div class="col-lg-8 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Monthly Appointment Volume — {{ $year }}</div>
                <div class="rpt-panel-body">
                    <canvas id="volumeChart" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- Paid vs Unpaid Doughnut --}}
        <div class="col-lg-4 col-12">
            <div class="rpt-panel h-100">
                <div class="rpt-panel-head">Payment Status</div>
                <div class="rpt-panel-body d-flex flex-column align-items-center justify-content-center">
                    <canvas id="paidChart" height="200" style="max-width:200px"></canvas>
                    <div class="d-flex gap-4 mt-3">
                        <div class="text-center">
                            <div class="fw-bold" style="color:#10b981">{{ $paidCount }}</div>
                            <small class="text-muted">Paid</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold" style="color:#f59e0b">{{ $unpaidCount }}</div>
                            <small class="text-muted">Unpaid</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- New vs Returning --}}
        <div class="col-lg-4 col-12">
            <div class="rpt-panel h-100">
                <div class="rpt-panel-head">New vs Returning Patients — {{ $year }}</div>
                <div class="rpt-panel-body d-flex flex-column align-items-center justify-content-center">
                    <canvas id="newRetChart" height="200" style="max-width:200px"></canvas>
                    <div class="d-flex gap-4 mt-3">
                        <div class="text-center">
                            <div class="fw-bold" style="color:#B1083C">{{ $newCount }}</div>
                            <small class="text-muted">New</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold" style="color:#8b5cf6">{{ $returningCount }}</div>
                            <small class="text-muted">Returning</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Table --}}
        <div class="col-lg-8 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Monthly Breakdown</div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-center">Appointments</th>
                                <th class="text-center">% of Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                            @for($m = 1; $m <= 12; $m++)
                            @php $cnt = $volume[$m]->total ?? 0; $pct = $totalAppts > 0 ? round($cnt/$totalAppts*100,1) : 0; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $months[$m] }} {{ $year }}</td>
                                <td class="text-center">{{ $cnt }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1 bg-light rounded" style="height:6px">
                                            <div class="rounded" style="height:6px;width:{{ $pct }}%;background:#B1083C"></div>
                                        </div>
                                        <small class="text-muted" style="min-width:36px">{{ $pct }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@include('admin.reports._styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const volumeData = @json(collect(range(1,12))->map(fn($m) => $volume[$m]->total ?? 0));

new Chart(document.getElementById('volumeChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Appointments',
            data: volumeData,
            backgroundColor: 'rgba(177,8,60,.75)',
            borderRadius: 5,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

new Chart(document.getElementById('paidChart'), {
    type: 'doughnut',
    data: {
        labels: ['Paid','Unpaid'],
        datasets: [{ data: [{{ $paidCount }}, {{ $unpaidCount }}], backgroundColor: ['#10b981','#f59e0b'], borderWidth: 0 }]
    },
    options: { plugins: { legend: { display: false } }, cutout: '68%' }
});

new Chart(document.getElementById('newRetChart'), {
    type: 'doughnut',
    data: {
        labels: ['New','Returning'],
        datasets: [{ data: [{{ $newCount }}, {{ $returningCount }}], backgroundColor: ['#B1083C','#8b5cf6'], borderWidth: 0 }]
    },
    options: { plugins: { legend: { display: false } }, cutout: '68%' }
});

document.getElementById('applyFilter').addEventListener('click', function () {
    const y = document.getElementById('filterYear').value;
    const c = document.getElementById('filterClinic')?.value ?? '';
    const d = document.getElementById('filterDoctor')?.value ?? '';
    let url = '{{ route("reports.appointments") }}?year=' + y;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    window.location.href = url;
});
</script>
@endsection
