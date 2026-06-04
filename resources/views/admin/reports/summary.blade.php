@extends('layouts.admin')
@section('title', 'P&L Summary Report')

@section('content')
<style>
    .pnl-stat { border-radius:10px; padding:18px 20px; color:#fff; }
    .pnl-panel { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
    .pnl-panel-head { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; padding:12px 18px; font-weight:600; font-size:.9rem; }
    .pnl-row { display:flex; justify-content:space-between; align-items:center; padding:10px 18px; border-bottom:1px solid #f0f0f0; font-size:.875rem; }
    .pnl-row:last-child { border-bottom:none; }
    .pnl-row.total-row { background:#f8f9fa; font-weight:700; font-size:.95rem; }
    .pnl-row.profit-positive { background:rgba(16,185,129,.08); color:#065f46; font-weight:700; font-size:1rem; }
    .pnl-row.profit-negative { background:rgba(239,68,68,.08); color:#991b1b; font-weight:700; font-size:1rem; }
    .filter-label { font-size:.8rem; font-weight:600; color:#6b7280; margin-bottom:3px; }
</style>

<div class="page-breadcrumb d-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">P&L Summary</div>
    <div class="ps-3">
        <nav><ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">P&L Summary</li>
        </ol></nav>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.summary') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <div class="filter-label">From</div>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <div class="filter-label">To</div>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div class="col-md-2">
                    <div class="filter-label">Clinic</div>
                    <select name="clinic_id" class="form-select form-select-sm">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ $clinicId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="filter-label">Doctor</div>
                    <select name="doctor_id" class="form-select form-select-sm">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $d)
                        <option value="{{ $d->id }}" {{ $doctorId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <div class="filter-label">&nbsp;</div>
                    <button type="submit" class="btn btn-sm text-white w-100" style="background:linear-gradient(90deg,#B1083C,#d13729);border:none">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── KPI Summary Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8)">
            <div class="small opacity-80">Service Revenue</div>
            <div class="fs-5 fw-bold mt-1">{{ auth()->user()->currency }}{{ number_format($serviceRevenue, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
            <div class="small opacity-80">Product Revenue</div>
            <div class="fs-5 fw-bold mt-1">{{ auth()->user()->currency }}{{ number_format($productRevenue, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#B1083C,#d13729)">
            <div class="small opacity-80">POS Sales</div>
            <div class="fs-5 fw-bold mt-1">{{ auth()->user()->currency }}{{ number_format($posSales, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:{{ $netProfit >= 0 ? 'linear-gradient(135deg,#10b981,#34d399)' : 'linear-gradient(135deg,#ef4444,#f87171)' }}">
            <div class="small opacity-80">Net Profit</div>
            <div class="fs-5 fw-bold mt-1">{{ auth()->user()->currency }}{{ number_format($netProfit, 0) }}</div>
        </div>
    </div>
</div>

{{-- ── Two column: P&L Statement + Chart ── --}}
<div class="row g-4 mb-4">

    {{-- P&L Statement --}}
    <div class="col-lg-5">
        <div class="pnl-panel h-100">
            <div class="pnl-panel-head"><i class="bi bi-receipt-cutoff me-1"></i>Profit & Loss Statement</div>

            {{-- INCOME --}}
            <div class="px-3 pt-3 pb-1">
                <div class="text-uppercase fw-bold" style="font-size:.72rem;color:#0ea5e9;letter-spacing:.05em">Income</div>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-calendar2-check me-2 text-muted"></i>Service Revenue</span>
                <span>{{ auth()->user()->currency }}{{ number_format($serviceRevenue, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-box-seam me-2 text-muted"></i>Appointment Product Revenue</span>
                <span>{{ auth()->user()->currency }}{{ number_format($productRevenue, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-cart3 me-2 text-muted"></i>POS Sales (Paid)</span>
                <span>{{ auth()->user()->currency }}{{ number_format($posSales, 2) }}</span>
            </div>
            <div class="pnl-row total-row">
                <span>Total Income</span>
                <span style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($totalIncome, 2) }}</span>
            </div>

            {{-- EXPENSES --}}
            <div class="px-3 pt-3 pb-1">
                <div class="text-uppercase fw-bold" style="font-size:.72rem;color:#ef4444;letter-spacing:.05em">Expenses</div>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-wallet2 me-2 text-muted"></i>Business Expenses</span>
                <span class="text-danger">{{ auth()->user()->currency }}{{ number_format($businessExpenses, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-people me-2 text-muted"></i>Salary Costs</span>
                <span class="text-danger">{{ auth()->user()->currency }}{{ number_format($salaryCosts, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-person-badge me-2 text-muted"></i>Doctor Shares</span>
                <span class="text-danger">{{ auth()->user()->currency }}{{ number_format($doctorShares, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-box me-2 text-muted"></i>Product COGS (Appt)</span>
                <span class="text-danger">{{ auth()->user()->currency }}{{ number_format($productCogs, 2) }}</span>
            </div>
            <div class="pnl-row total-row">
                <span>Total Expenses</span>
                <span class="text-danger">{{ auth()->user()->currency }}{{ number_format($totalExpenses, 2) }}</span>
            </div>

            {{-- NET PROFIT --}}
            <div class="pnl-row {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Net Profit / Loss</span>
                <span>{{ auth()->user()->currency }}{{ number_format($netProfit, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Trend Chart --}}
    <div class="col-lg-7">
        <div class="pnl-panel h-100">
            <div class="pnl-panel-head"><i class="bi bi-graph-up me-1"></i>Monthly Income vs Expense vs Profit</div>
            <div class="p-3">
                @if(count($trendMonths) > 0)
                <canvas id="trendChart" height="{{ count($trendMonths) == 1 ? '120' : '180' }}"></canvas>
                @else
                <p class="text-muted text-center py-5">No data for the selected period.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Breakdown Bars ── --}}
<div class="row g-4 mb-4">

    {{-- Income Breakdown --}}
    <div class="col-md-6">
        <div class="pnl-panel">
            <div class="pnl-panel-head"><i class="bi bi-pie-chart me-1"></i>Income Breakdown</div>
            <div class="p-4">
                @php
                    $incomeItems = [
                        ['label' => 'Service Revenue',   'value' => $serviceRevenue,  'color' => '#0ea5e9'],
                        ['label' => 'Product Revenue',   'value' => $productRevenue,  'color' => '#8b5cf6'],
                        ['label' => 'POS Sales',         'value' => $posSales,        'color' => '#B1083C'],
                    ];
                @endphp
                @foreach($incomeItems as $item)
                @php $pct = $totalIncome > 0 ? round($item['value'] / $totalIncome * 100, 1) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem">
                        <span class="fw-semibold">{{ $item['label'] }}</span>
                        <span>{{ auth()->user()->currency }}{{ number_format($item['value'], 0) }} &nbsp;<span class="text-muted">({{ $pct }}%)</span></span>
                    </div>
                    <div class="bg-light rounded" style="height:8px">
                        <div class="rounded" style="height:8px;width:{{ $pct }}%;background:{{ $item['color'] }};transition:width .4s"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Expense Breakdown --}}
    <div class="col-md-6">
        <div class="pnl-panel">
            <div class="pnl-panel-head"><i class="bi bi-pie-chart-fill me-1"></i>Expense Breakdown</div>
            <div class="p-4">
                @php
                    $expenseItems = [
                        ['label' => 'Business Expenses', 'value' => $businessExpenses, 'color' => '#ef4444'],
                        ['label' => 'Salary Costs',      'value' => $salaryCosts,      'color' => '#f97316'],
                        ['label' => 'Doctor Shares',     'value' => $doctorShares,     'color' => '#f59e0b'],
                        ['label' => 'Product COGS',      'value' => $productCogs,      'color' => '#6b7280'],
                    ];
                @endphp
                @foreach($expenseItems as $item)
                @php $pct = $totalExpenses > 0 ? round($item['value'] / $totalExpenses * 100, 1) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem">
                        <span class="fw-semibold">{{ $item['label'] }}</span>
                        <span>{{ auth()->user()->currency }}{{ number_format($item['value'], 0) }} &nbsp;<span class="text-muted">({{ $pct }}%)</span></span>
                    </div>
                    <div class="bg-light rounded" style="height:8px">
                        <div class="rounded" style="height:8px;width:{{ $pct }}%;background:{{ $item['color'] }};transition:width .4s"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Monthly Trend Table ── --}}
@if(count($trendMonths) > 1)
<div class="pnl-panel mb-4">
    <div class="pnl-panel-head"><i class="bi bi-table me-1"></i>Monthly Breakdown</div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0" style="font-size:.875rem">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Month</th>
                    <th class="text-end">Income</th>
                    <th class="text-end">Expenses</th>
                    <th class="text-end pe-3">Net Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trendMonths as $i => $month)
                @php
                    $mIncome  = $trendIncome[$i] ?? 0;
                    $mExpense = $trendExpense[$i] ?? 0;
                    $mProfit  = $trendProfit[$i] ?? 0;
                @endphp
                <tr>
                    <td class="ps-3 fw-semibold">{{ $month }}</td>
                    <td class="text-end" style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($mIncome, 0) }}</td>
                    <td class="text-end text-danger">{{ auth()->user()->currency }}{{ number_format($mExpense, 0) }}</td>
                    <td class="text-end pe-3 fw-semibold {{ $mProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $mProfit >= 0 ? '+' : '' }}{{ auth()->user()->currency }}{{ number_format($mProfit, 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <td class="ps-3 fw-bold">Total</td>
                    <td class="text-end fw-bold" style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($totalIncome, 0) }}</td>
                    <td class="text-end fw-bold text-danger">{{ auth()->user()->currency }}{{ number_format($totalExpenses, 0) }}</td>
                    <td class="text-end pe-3 fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $netProfit >= 0 ? '+' : '' }}{{ auth()->user()->currency }}{{ number_format($netProfit, 0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const months   = @json($trendMonths);
    const income   = @json($trendIncome);
    const expense  = @json($trendExpense);
    const profit   = @json($trendProfit);
    const currency = '{{ auth()->user()->currency ?? "" }}';

    if (!months.length) return;

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Income',
                    data: income,
                    backgroundColor: 'rgba(14,165,233,.7)',
                    borderColor: '#0ea5e9',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y',
                },
                {
                    label: 'Expenses',
                    data: expense,
                    backgroundColor: 'rgba(239,68,68,.7)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y',
                },
                {
                    label: 'Net Profit',
                    data: profit,
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.15)',
                    tension: .3,
                    borderWidth: 2,
                    pointBackgroundColor: profit.map(v => v >= 0 ? '#10b981' : '#ef4444'),
                    yAxisID: 'y',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + currency + ctx.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 0 })
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => currency + v.toLocaleString() }
                }
            }
        }
    });
})();
</script>
@endsection
