@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1 align-items-center">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2" style="color:#B1083C"></i>Products Sold Report</h4>
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
                <div class="rpt-stat-lbl">Product Revenue</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-box-seam"></i></div>
                <div class="rpt-stat-num">{{ number_format($totalQty) }}</div>
                <div class="rpt-stat-lbl">Units Sold</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <div class="rpt-stat-card">
                <div class="rpt-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-arrow-return-left"></i></div>
                <div class="rpt-stat-num">PKR {{ number_format($totalRefunds, 0) }}</div>
                <div class="rpt-stat-lbl">Total Refunds</div>
            </div>
        </div>
    </div>

    <div class="row mx-1 g-4">
        {{-- Top 10 Chart --}}
        <div class="col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Top Products by Revenue</div>
                <div class="rpt-panel-body">
                    <canvas id="productChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Full Table --}}
        <div class="col-12">
            <div class="rpt-panel">
                <div class="rpt-panel-head">Product Sales Details</div>
                <div class="rpt-panel-body p-0">
                    <table class="table table-hover mb-0 rpt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Units Sold</th>
                                <th class="text-center">In Appointments</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Doctor Share</th>
                                <th class="text-end">Clinic Share</th>
                                <th class="text-center">Returned</th>
                                <th class="text-center">Return Rate</th>
                                <th class="text-end">Refunded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sold as $i => $row)
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold small">{{ $row->product_name }}</td>
                                <td class="text-center">{{ $row->qty_sold }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $row->in_appointments }}</span></td>
                                <td class="text-end fw-semibold" style="color:#B1083C">PKR {{ number_format($row->revenue, 0) }}</td>
                                <td class="text-end text-muted small">PKR {{ number_format($row->doctor_share, 0) }}</td>
                                <td class="text-end text-muted small">PKR {{ number_format($row->clinic_share, 0) }}</td>
                                <td class="text-center">
                                    @if($row->returned_qty > 0)
                                        <span class="badge bg-danger">{{ $row->returned_qty }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->return_rate > 0)
                                        <span class="badge {{ $row->return_rate > 20 ? 'bg-danger' : ($row->return_rate > 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ $row->return_rate }}%
                                        </span>
                                    @else
                                        <span class="text-muted small">0%</span>
                                    @endif
                                </td>
                                <td class="text-end small text-muted">
                                    {{ $row->refund_total > 0 ? 'PKR ' . number_format($row->refund_total, 0) : '—' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">No product sales data for this period.</td></tr>
                            @endforelse
                        </tbody>
                        @if($sold->isNotEmpty())
                        <tfoot class="table-light">
                            <tr class="fw-semibold">
                                <td colspan="2">Total</td>
                                <td class="text-center">{{ $totalQty }}</td>
                                <td></td>
                                <td class="text-end" style="color:#B1083C">PKR {{ number_format($totalRevenue, 0) }}</td>
                                <td class="text-end">PKR {{ number_format($sold->sum('doctor_share'), 0) }}</td>
                                <td class="text-end">PKR {{ number_format($sold->sum('clinic_share'), 0) }}</td>
                                <td class="text-center">{{ $sold->sum('returned_qty') }}</td>
                                <td></td>
                                <td class="text-end">PKR {{ number_format($totalRefunds, 0) }}</td>
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
const topSold    = @json($sold->take(10));
const pLabels    = topSold.map(r => r.product_name);
const pRevenue   = topSold.map(r => r.revenue);
const pReturned  = topSold.map(r => r.refund_total);

new Chart(document.getElementById('productChart'), {
    type: 'bar',
    data: {
        labels: pLabels,
        datasets: [
            { label: 'Revenue (PKR)', data: pRevenue, backgroundColor: 'rgba(177,8,60,.8)', borderRadius: 4 },
            { label: 'Refunds (PKR)', data: pReturned, backgroundColor: 'rgba(239,68,68,.6)', borderRadius: 4 },
        ]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { position: 'top' } },
        scales: { x: { beginAtZero: true, stacked: false } }
    }
});

document.getElementById('applyFilter').addEventListener('click', function () {
    const y = document.getElementById('filterYear').value;
    const m = document.getElementById('filterMonth').value;
    const c = document.getElementById('filterClinic')?.value ?? '';
    const d = document.getElementById('filterDoctor')?.value ?? '';
    let url = '{{ route("reports.products-sold") }}?year=' + y;
    if (m) url += '&month=' + m;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    window.location.href = url;
});
</script>
@endsection
