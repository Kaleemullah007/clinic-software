@extends('layouts.admin')

@section('content')
<div class="container-fluid pb-5">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="row pt-3 px-1 align-items-center mb-3">
        <div class="col">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-speedometer2 me-2" style="color:#B1083C;"></i>Dashboard
            </h4>
            <small class="text-muted">
                {{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}
            </small>
        </div>
    </div>

    {{-- ── Filter bar ──────────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('dashboard') }}" id="dashFilterForm">
        <div class="card border-0 shadow-sm mb-4 px-3 py-3">
            <div class="row g-2 align-items-center flex-wrap">

                {{-- Period buttons --}}
                <div class="col-12 col-xl-auto">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach([
                            'this_week'  => 'This Week',
                            'last_week'  => 'Last Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year'  => 'This Year',
                            'custom'     => 'Custom',
                        ] as $key => $label)
                        <button type="button"
                                class="btn btn-sm period-btn {{ $period === $key ? 'text-white' : 'btn-outline-secondary' }}"
                                style="{{ $period === $key ? 'background:linear-gradient(90deg,#B1083C,#d13729);border-color:#B1083C;' : '' }}"
                                data-period="{{ $key }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="period" id="periodInput" value="{{ $period }}">
                </div>

                {{-- Custom range --}}
                <div class="col-auto {{ $period !== 'custom' ? 'd-none' : '' }}" id="customRangeWrap">
                    <div class="d-flex align-items-center gap-2">
                        <input type="date" name="date_from" id="dateFrom"
                               class="form-control form-control-sm border-secondary"
                               value="{{ $dateFrom->format('Y-m-d') }}" style="max-width:145px;">
                        <span class="text-muted small">to</span>
                        <input type="date" name="date_to" id="dateTo"
                               class="form-control form-control-sm border-secondary"
                               value="{{ $dateTo->format('Y-m-d') }}" style="max-width:145px;">
                    </div>
                </div>

                {{-- Clinic --}}
                @if($clinics->count())
                <div class="col-auto">
                    <select name="clinic_id" class="form-select form-select-sm border-secondary" onchange="this.form.submit()" style="min-width:150px;">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}" {{ request('clinic_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Doctor (super admin only) --}}
                @if(auth()->user()->isSuperAdmin() && $doctors->count())
                <div class="col-auto">
                    <select name="doctor_id" class="form-select form-select-sm border-secondary" onchange="this.form.submit()" style="min-width:160px;">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Apply / Reset --}}
                <div class="col-auto ms-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white px-4"
                            style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary px-3">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Stat cards — CSS Grid guarantees pixel-perfect equal width & height ── --}}
    <div class="stat-grid mb-4">

        {{-- Appointments --}}
        @can('appointments.view')
        <a href="{{ route('appointments.index') }}" class="text-decoration-none stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#B1083C,#d13729);">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <div class="stat-value">{{ number_format($totalAppointments) }}</div>
                <div class="stat-label">Appointments</div>
                <div class="stat-sub">
                    <span class="text-success small"><i class="bi bi-check-circle me-1"></i>{{ $paidCount }} Paid</span>
                    <span class="text-danger small ms-2"><i class="bi bi-clock me-1"></i>{{ $unpaidCount }} Unpaid</span>
                </div>
            </div>
        </a>
        @endcan

        {{-- Revenue --}}
        @can('appointments.view')
        <div class="stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#198754,#20c997);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value">{{ number_format($revenue) }}</div>
                <div class="stat-label">Revenue (PKR)</div>
                <div class="stat-sub">
                    <span class="text-muted small">{{ $dateFrom->format('d M') }} – {{ $dateTo->format('d M') }}</span>
                </div>
            </div>
        </div>
        @endcan

        {{-- Products --}}
        @can('products.view')
        <a href="{{ route('products.index') }}" class="text-decoration-none stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#0d6efd,#6ea8fe);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-value">{{ number_format($totalProducts) }}</div>
                <div class="stat-label">Products</div>
                <div class="stat-sub">
                    <span class="text-success small"><i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>{{ $activeProducts }} Active</span>
                    <span class="text-secondary small ms-2"><i class="bi bi-circle me-1" style="font-size:7px;"></i>{{ $inactiveProducts }} Inactive</span>
                </div>
            </div>
        </a>
        @endcan

        {{-- Low Stock — always visible --}}
        @can('products.view')
        <a href="{{ route('inventory.index') }}" class="text-decoration-none stat-grid-item">
            @php $lowCount = $lowStockProducts->count(); @endphp
            <div class="stat-card {{ $lowCount > 0 ? 'stat-card-danger' : '' }}">
                <div class="stat-icon" style="background:linear-gradient(135deg,{{ $lowCount > 0 ? '#dc3545,#f28b82' : '#198754,#20c997' }});">
                    <i class="bi bi-{{ $lowCount > 0 ? 'exclamation-triangle' : 'shield-check' }}"></i>
                </div>
                <div class="stat-value {{ $lowCount > 0 ? 'text-danger' : 'text-success' }}">{{ $lowCount }}</div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-sub">
                    @if($lowCount > 0)
                        <span class="text-danger small">Below {{ $stockAlertQty }} units</span>
                    @else
                        <span class="text-success small">All stocked OK</span>
                    @endif
                </div>
            </div>
        </a>
        @endcan

        {{-- Users --}}
        @can('users.view')
        <a href="{{ route('users.index') }}" class="text-decoration-none stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#6f42c1,#a56eff);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ number_format($users) }}</div>
                <div class="stat-label">Users</div>
                <div class="stat-sub">
                    <span class="text-muted small">All staff & patients</span>
                </div>
            </div>
        </a>
        @endcan

        {{-- Services --}}
        @can('category.view')
        <a href="{{ route('category.index') }}" class="text-decoration-none stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#fd7e14,#ffc107);">
                    <i class="bi bi-bounding-box"></i>
                </div>
                <div class="stat-value">{{ $categories }}</div>
                <div class="stat-label">Services</div>
                <div class="stat-sub">
                    <span class="text-muted small">Treatment categories</span>
                </div>
            </div>
        </a>
        @endcan

        {{-- Prescriptions --}}
        @can('prescription.view')
        <a href="{{ route('prescription.index') }}" class="text-decoration-none stat-grid-item">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#0dcaf0,#6edff6);">
                    <i class="bi bi-capsule"></i>
                </div>
                <div class="stat-value">{{ $prescriptions }}</div>
                <div class="stat-label">Prescriptions</div>
                <div class="stat-sub">
                    <span class="text-muted small">Total issued</span>
                </div>
            </div>
        </a>
        @endcan

    </div>

    {{-- ── Low stock alert banner — always visible ────────────────────────── --}}
    @can('products.view')
    @php $lowCount = $lowStockProducts->count(); @endphp
    <div class="card border-0 shadow-sm mb-4"
         style="border-left:4px solid {{ $lowCount > 0 ? '#dc3545' : '#198754' }} !important;">
        <div class="card-header border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center
                    {{ $lowCount > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}">
            <h6 class="fw-bold mb-0 {{ $lowCount > 0 ? 'text-danger' : 'text-success' }}">
                @if($lowCount > 0)
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Low Stock Alert — {{ $lowCount }} product(s) below {{ $stockAlertQty }} units
                @else
                    <i class="bi bi-shield-check me-2"></i>
                    Stock Healthy — All products are above the alert threshold ({{ $stockAlertQty }} units)
                @endif
            </h6>
            <a href="{{ route('inventory.index') }}" class="btn btn-sm {{ $lowCount > 0 ? 'btn-outline-danger' : 'btn-outline-success' }}">
                View Inventory <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        @if($lowCount > 0)
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Product</th>
                            <th class="text-center">Current Qty</th>
                            <th class="text-center">Alert Level</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $lp)
                        @php $qty = $lp->inventory?->quantity ?? 0; @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold small">{{ $lp->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $lp->description ?? '' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $qty <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} px-2">
                                    {{ $qty }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-2">{{ $stockAlertQty }}</span>
                            </td>
                            <td class="text-center">
                                @if($qty <= 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle small">Low Stock</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('inventory.show', $lp->id) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        @endif {{-- end if lowCount > 0 --}}
    </div>
    @endcan

    {{-- ── Charts row ──────────────────────────────────────────────────────── --}}
    @can('appointments.view')
    <div class="row g-3 mb-4">

        {{-- Line: appointments over time --}}
        <div class="col-lg-8 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2" style="color:#B1083C;"></i>Appointments Over Time
                    </h6>
                    <small class="text-muted">{{ $dateFrom->format('d M') }} – {{ $dateTo->format('d M Y') }}</small>
                </div>
                <div class="card-body px-3 py-3">
                    @if(array_sum($timeValues) > 0)
                        <canvas id="timeChart" style="max-height:260px;"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bar-chart d-block fs-1 mb-2 opacity-25"></i>
                            No data for this period
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Doughnut: paid vs unpaid --}}
        <div class="col-lg-4 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-pie-chart me-2" style="color:#B1083C;"></i>Payment Status
                    </h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-3">
                    @if($totalAppointments > 0)
                        <canvas id="paidChart" style="max-height:200px;max-width:200px;"></canvas>
                        <div class="d-flex gap-3 mt-3">
                            <span class="small"><span class="badge bg-success me-1">&nbsp;</span>Paid: {{ $paidCount }}</span>
                            <span class="small"><span class="badge bg-danger me-1">&nbsp;</span>Unpaid: {{ $unpaidCount }}</span>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-pie-chart d-block fs-1 mb-2 opacity-25"></i>No data
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bar: per doctor --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-person-badge me-2" style="color:#B1083C;"></i>Appointments per Doctor
                    </h6>
                    @if($doctorChartData->count())
                    <small class="text-muted">Top {{ $doctorChartData->count() }}</small>
                    @endif
                </div>
                <div class="card-body px-3 py-3">
                    @if($doctorChartData->count())
                        <canvas id="doctorChart" style="max-height:220px;"></canvas>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No appointments in this period.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
    @endcan

    {{-- ── Latest appointments table ────────────────────────────────────────── --}}
    @can('appointments.view')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2" style="color:#B1083C;"></i>Latest Appointments
            </h6>
            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="40">#</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Services</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="60">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestAppointments as $i => $appt)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold small lh-tight">{{ $appt->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $appt->phone }}</div>
                            </td>
                            <td class="small">{{ optional($appt->doctor)->name ?? '—' }}</td>
                            <td class="small text-nowrap">
                                {{ $appt->date ? \Carbon\Carbon::parse($appt->date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                @foreach($appt->appointmentService->take(2) as $svc)
                                    <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $svc->name }}</span>
                                @endforeach
                                @if($appt->appointmentService->count() > 2)
                                    <span class="badge bg-secondary" style="font-size:10px;">+{{ $appt->appointmentService->count() - 2 }}</span>
                                @endif
                            </td>
                            <td class="text-end small fw-semibold">
                                PKR {{ number_format($appt->subtotal_discounted_price_after_discount ?? 0) }}
                            </td>
                            <td class="text-center">
                                @if($appt->is_paid === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Paid</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:10px;">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('appointments.show', $appt->id) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x d-block fs-2 mb-2 opacity-25"></i>
                                No appointments in this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan

</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {

    // ── Period buttons ────────────────────────────────────────────────────────
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const period      = this.dataset.period;
            const customWrap  = document.getElementById('customRangeWrap');
            document.getElementById('periodInput').value = period;

            if (period === 'custom') {
                customWrap.classList.remove('d-none');
            } else {
                customWrap.classList.add('d-none');
                document.getElementById('dashFilterForm').submit();
            }
        });
    });

    ['dateFrom', 'dateTo'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => {
            const f = document.getElementById('dateFrom').value;
            const t = document.getElementById('dateTo').value;
            if (f && t) document.getElementById('dashFilterForm').submit();
        });
    });

    // ── Chart.js ─────────────────────────────────────────────────────────────
    const rgba = (hex, a) => {
        const r = parseInt(hex.slice(1,3),16);
        const g = parseInt(hex.slice(3,5),16);
        const b = parseInt(hex.slice(5,7),16);
        return `rgba(${r},${g},${b},${a})`;
    };

    // Line chart
    const timeCtx = document.getElementById('timeChart');
    if (timeCtx) {
        new Chart(timeCtx, {
            type: 'line',
            data: {
                labels: @json($timeLabels),
                datasets: [{
                    label: 'Appointments',
                    data : @json($timeValues),
                    borderColor: '#B1083C',
                    backgroundColor: rgba('#B1083C', 0.07),
                    borderWidth: 2.5,
                    pointBackgroundColor: '#B1083C',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: c => ` ${c.parsed.y} appointment${c.parsed.y !== 1 ? 's' : ''}` } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f5f5f5' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 45 } }
                }
            }
        });
    }

    // Doughnut chart
    const paidCtx = document.getElementById('paidChart');
    if (paidCtx) {
        new Chart(paidCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Unpaid'],
                datasets: [{
                    data: [{{ $paidCount }}, {{ $unpaidCount }}],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => {
                                const total = {{ $totalAppointments }};
                                const pct   = total ? Math.round(c.parsed / total * 100) : 0;
                                return ` ${c.label}: ${c.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Bar chart: per doctor
    const docCtx = document.getElementById('doctorChart');
    if (docCtx) {
        const palette = ['#B1083C','#d13729','#0d6efd','#198754','#6f42c1','#fd7e14','#0dcaf0','#20c997','#ffc107','#6c757d'];
        const labels  = @json($doctorChartData->pluck('label'));
        const values  = @json($doctorChartData->pluck('value'));

        new Chart(docCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Appointments',
                    data : values,
                    backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: c => ` ${c.parsed.y} appointment${c.parsed.y !== 1 ? 's' : ''}` } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f5f5f5' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

})();
</script>

<style>
/* ── CSS Grid for stat cards — guarantees pixel-perfect equal width & height ── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);   /* 2 per row on mobile */
    gap: 1rem;
    align-items: stretch;                     /* all rows same height */
}
@media (min-width: 576px)  { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 992px)  { .stat-grid { grid-template-columns: repeat(4, 1fr); } }

/* Each grid cell is a flex column so the card stretches to 100% */
.stat-grid-item {
    display: flex;
    flex-direction: column;
}

/* The white card — fills the grid cell completely */
.stat-card {
    flex: 1;                                  /* stretch to fill cell height */
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
    color: inherit;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
}
.stat-card-danger {
    border-left: 4px solid #dc3545;
}

/* Icon pill */
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    color: #fff;
    flex-shrink: 0;
}

/* Number */
.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
    color: #1a1a2e;
    margin-top: 4px;
}

/* Label */
.stat-label {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
}

/* Sub-stats row */
.stat-sub {
    margin-top: auto;
    padding-top: .5rem;
    border-top: 1px solid #f0f0f0;
    font-size: .8rem;
}

/* Cards general */
.card { border-radius: 12px !important; }
.card-header { border-radius: 12px 12px 0 0 !important; }

/* Filter period buttons */
.period-btn { border-radius: 6px !important; font-size: 12px; }
</style>
@endsection
