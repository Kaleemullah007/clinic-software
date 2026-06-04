@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1 align-items-center">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0"><i class="bi bi-people me-2" style="color:#B1083C"></i>Patient Report</h4>
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
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Retention Window</label>
            <select id="filterRetention" class="form-select form-select-sm border-secondary" style="width:160px">
                <option value="30"  {{ $retentionDays == 30  ? 'selected' : '' }}>30 days inactive</option>
                <option value="60"  {{ $retentionDays == 60  ? 'selected' : '' }}>60 days inactive</option>
                <option value="90"  {{ $retentionDays == 90  ? 'selected' : '' }}>90 days inactive</option>
                <option value="180" {{ $retentionDays == 180 ? 'selected' : '' }}>180 days inactive</option>
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
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-person-plus"></i></div>
                <div class="rpt-stat-num">{{ number_format($newCount) }}</div>
                <div class="rpt-stat-lbl">New Patients in {{ $year }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="bi bi-arrow-repeat"></i></div>
                <div class="rpt-stat-num">{{ number_format($returningCount) }}</div>
                <div class="rpt-stat-lbl">Returning Patients</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-trophy"></i></div>
                <div class="rpt-stat-num">{{ $topPatients->first()?->name ?? '—' }}</div>
                <div class="rpt-stat-lbl">Top Spender</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-person-x"></i></div>
                <div class="rpt-stat-num">{{ number_format($lostPatients->count()) }}</div>
                <div class="rpt-stat-lbl">Inactive ({{ $retentionDays }}+ days)</div>
            </div>
        </div>
    </div>

    <div class="row mx-1 g-4">
        {{-- New Patients Monthly Chart --}}
        <div class="col-lg-8 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">New Patients per Month — {{ $year }}</div>
                <div class="rpt-panel-body">
                    <canvas id="newPatChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- New vs Returning Doughnut --}}
        <div class="col-lg-4 col-12">
            <div class="rpt-panel h-100">
                <div class="rpt-panel-head">New vs Returning — {{ $year }}</div>
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

        {{-- Top Patients by Spend --}}
        <div class="col-lg-6 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head"><i class="bi bi-trophy me-1" style="color:#f59e0b"></i>Top 20 Patients by Spend (All Time)</div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th class="text-center">Visits</th>
                                <th class="text-end">Total Paid</th>
                                <th>Last Visit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPatients as $i => $p)
                            <tr>
                                <td>
                                    @if($i === 0) <i class="bi bi-trophy-fill" style="color:#f59e0b"></i>
                                    @elseif($i === 1) <i class="bi bi-trophy-fill" style="color:#9ca3af"></i>
                                    @elseif($i === 2) <i class="bi bi-trophy-fill" style="color:#b45309"></i>
                                    @else <span class="text-muted small">{{ $i+1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $p->name }}</div>
                                    <small class="text-muted">{{ $p->phone }}</small>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $p->visit_count }}</span></td>
                                <td class="text-end fw-semibold" style="color:#B1083C">PKR {{ number_format($p->total_paid, 0) }}</td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($p->last_visit)->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No patient data available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Inactive Patients --}}
        <div class="col-lg-6 col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head"><i class="bi bi-person-x me-1" style="color:#ef4444"></i>Patients Inactive {{ $retentionDays }}+ Days</div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th class="text-center">Visits</th>
                                <th>Last Visit</th>
                                <th class="text-end">Lifetime Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lostPatients as $p)
                            @php $daysAgo = \Carbon\Carbon::parse($p->last_visit)->diffInDays(now()); @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $p->name }}</div>
                                    <small class="text-muted">{{ $p->phone }}</small>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $p->total_visits }}</span></td>
                                <td>
                                    <div class="small">{{ \Carbon\Carbon::parse($p->last_visit)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $daysAgo }} days ago</small>
                                </td>
                                <td class="text-end small text-muted">PKR {{ number_format($p->lifetime_value, 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No inactive patients found.</td></tr>
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
@include('admin.reports._styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const newPatData = @json(collect(range(1,12))->map(fn($m) => $monthlyNew[$m] ?? 0)->values());

new Chart(document.getElementById('newPatChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'New Patients',
            data: newPatData,
            borderColor: '#B1083C',
            backgroundColor: 'rgba(177,8,60,.1)',
            borderWidth: 2,
            pointBackgroundColor: '#B1083C',
            fill: true,
            tension: 0.4,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
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
    const r = document.getElementById('filterRetention').value;
    const c = document.getElementById('filterClinic')?.value ?? '';
    const d = document.getElementById('filterDoctor')?.value ?? '';
    let url = '{{ route("reports.patients") }}?year=' + y + '&retention_days=' + r;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    window.location.href = url;
});
</script>
@endsection
