@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1 align-items-center">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0"><i class="bi bi-scissors me-2" style="color:#B1083C"></i>Service Revenue Report</h4>
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
            <label class="form-label small text-muted mb-1">Month</label>
            <select id="filterMonth" class="form-select form-select-sm border-secondary" style="width:130px">
                <option value="">All Months</option>
                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $mn)
                    <option value="{{ $i+1 }}" {{ $month == ($i+1) ? 'selected' : '' }}>{{ $mn }}</option>
                @endforeach
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
            <label class="form-label small text-muted mb-1">Service</label>
            <select id="filterService" class="form-select form-select-sm border-secondary" style="width:180px">
                <option value="">All Services</option>
                @foreach($services as $s)
                <option value="{{ $s }}" {{ ($serviceName ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button id="applyFilter" class="btn btn-sm btn-danger">
                <i class="bi bi-funnel me-1"></i>Apply
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mx-1 g-3 mb-4">
        <div class="col-lg-4 col-md-4 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-currency-dollar"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($totalRevenue, 0) }}</div>
                <div class="rpt-stat-lbl">Total Service Revenue</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-clipboard2-check"></i></div>
                <div class="rpt-stat-num">{{ number_format($totalBookings) }}</div>
                <div class="rpt-stat-lbl">Total Bookings</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="bi bi-tag"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($totalDiscount, 0) }}</div>
                <div class="rpt-stat-lbl">Total Discounts Given</div>
            </div>
        </div>
    </div>

    <div class="row mx-1 g-4">
        {{-- Bar Chart --}}
        <div class="col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Revenue by Service</div>
                <div class="rpt-panel-body">
                    <canvas id="serviceChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Service Breakdown</div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">List Price Total</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Revenue Collected</th>
                                <th class="text-end">Avg per Booking</th>
                                <th class="text-center">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $i => $row)
                            @php
                                $share = $totalRevenue > 0 ? round($row->revenue / $totalRevenue * 100, 1) : 0;
                                $avg   = $row->booking_count > 0 ? $row->revenue / $row->booking_count : 0;
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row->service_name }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $row->booking_count }}</span></td>
                                <td class="text-end text-muted">PKR {{ number_format($row->gross, 0) }}</td>
                                <td class="text-end" style="color:#f59e0b">– PKR {{ number_format($row->total_discount, 0) }}</td>
                                <td class="text-end fw-semibold" style="color:#B1083C">PKR {{ number_format($row->revenue, 0) }}</td>
                                <td class="text-end text-muted small">PKR {{ number_format($avg, 0) }}</td>
                                <td class="text-center" style="min-width:120px">
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="flex-grow-1 bg-light rounded" style="height:6px">
                                            <div class="rounded" style="height:6px;width:{{ $share }}%;background:#B1083C"></div>
                                        </div>
                                        <small class="text-muted" style="min-width:34px">{{ $share }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No service data for this period.</td></tr>
                            @endforelse
                        </tbody>
                        @if($data->isNotEmpty())
                        <tfoot class="table-light">
                            <tr class="fw-semibold">
                                <td colspan="2">Total</td>
                                <td class="text-center">{{ $totalBookings }}</td>
                                <td class="text-end">PKR {{ number_format($data->sum('gross'), 0) }}</td>
                                <td class="text-end" style="color:#f59e0b">– PKR {{ number_format($totalDiscount, 0) }}</td>
                                <td class="text-end" style="color:#B1083C">PKR {{ number_format($totalRevenue, 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
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
const labels  = @json($data->pluck('service_name'));
const revenue = @json($data->pluck('revenue'));

new Chart(document.getElementById('serviceChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Revenue (PKR)',
            data: revenue,
            backgroundColor: labels.map((_, i) => i === 0 ? 'rgba(177,8,60,.85)' : 'rgba(177,8,60,' + (0.55 - i * 0.03).toFixed(2) + ')'),
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

document.getElementById('applyFilter').addEventListener('click', function () {
    const y = document.getElementById('filterYear').value;
    const m = document.getElementById('filterMonth').value;
    const c = document.getElementById('filterClinic')?.value ?? '';
    const d = document.getElementById('filterDoctor')?.value ?? '';
    const s = document.getElementById('filterService').value;
    let url = '{{ route("reports.services") }}?year=' + y;
    if (m) url += '&month=' + m;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    if (s) url += '&service_name=' + encodeURIComponent(s);
    window.location.href = url;
});
</script>
@endsection
