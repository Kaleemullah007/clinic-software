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

    /* ── Date Range Dropdown ── */
    .date-preset-wrap { position:relative; }
    .date-preset-wrap .form-select { padding-right:2rem; }
    #custom-range-inputs { display:none; }
    #custom-range-inputs.show { display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; }

    /* ── Loading overlay ── */
    #summary-loading {
        display:none;
        position:fixed; inset:0; z-index:9999;
        background:rgba(255,255,255,.55);
        align-items:center; justify-content:center;
    }
    #summary-loading.show { display:flex; }
    .spinner-ring {
        width:48px; height:48px;
        border:4px solid #f3f3f3;
        border-top:4px solid #B1083C;
        border-radius:50%;
        animation:spin .7s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* ── Progress bar animate ── */
    .bar-fill { transition:width .5s ease; }
</style>

{{-- Loading overlay --}}
<div id="summary-loading"><div class="spinner-ring"></div></div>

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
        <div class="row g-2 align-items-end" id="filter-row">

            {{-- Date Preset Dropdown --}}
            <div class="col-md-3">
                <div class="filter-label">Date Range</div>
                <select id="date-preset" class="form-select form-select-sm">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month" selected>This Month</option>
                    <option value="this_year">This Year</option>
                    <option value="last_year">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            {{-- Custom date inputs — hidden unless Custom Range selected --}}
            <div class="col-md-4" id="custom-range-inputs">
                <div class="d-flex gap-2 align-items-end">
                    <div>
                        <div class="filter-label">From</div>
                        <input type="date" id="filter-from" value="{{ $from }}" class="form-control form-control-sm">
                    </div>
                    <div>
                        <div class="filter-label">To</div>
                        <input type="date" id="filter-to" value="{{ $to }}" class="form-control form-control-sm">
                    </div>
                </div>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div class="col-md-2">
                <div class="filter-label">Clinic</div>
                <select id="filter-clinic" class="form-select form-select-sm">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $c)
                    <option value="{{ $c->id }}" {{ $clinicId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="filter-label">Doctor</div>
                <select id="filter-doctor" class="form-select form-select-sm">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ $doctorId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Apply button — only visible for Custom Range --}}
            <div class="col-md-2" id="apply-btn-wrap" style="display:none">
                <div class="filter-label">&nbsp;</div>
                <button id="apply-filter" class="btn btn-sm text-white w-100" style="background:linear-gradient(90deg,#B1083C,#d13729);border:none">
                    <i class="bi bi-funnel me-1"></i>Apply
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── KPI Summary Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8)">
            <div class="small opacity-80">Service Revenue</div>
            <div class="fs-5 fw-bold mt-1" id="kpi-service-revenue">{{ auth()->user()->currency }}{{ number_format($serviceRevenue, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
            <div class="small opacity-80">Product Revenue</div>
            <div class="fs-5 fw-bold mt-1" id="kpi-product-revenue">{{ auth()->user()->currency }}{{ number_format($productRevenue, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" style="background:linear-gradient(135deg,#B1083C,#d13729)">
            <div class="small opacity-80">POS Sales</div>
            <div class="fs-5 fw-bold mt-1" id="kpi-pos-sales">{{ auth()->user()->currency }}{{ number_format($posSales, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pnl-stat" id="kpi-net-profit-card" style="background:{{ $netProfit >= 0 ? 'linear-gradient(135deg,#10b981,#34d399)' : 'linear-gradient(135deg,#ef4444,#f87171)' }}">
            <div class="small opacity-80">Net Profit</div>
            <div class="fs-5 fw-bold mt-1" id="kpi-net-profit">{{ auth()->user()->currency }}{{ number_format($netProfit, 0) }}</div>
        </div>
    </div>
</div>

{{-- ── Two column: P&L Statement + Chart ── --}}
<div class="row g-4 mb-4">

    {{-- P&L Statement --}}
    <div class="col-lg-5">
        <div class="pnl-panel h-100">
            <div class="pnl-panel-head"><i class="bi bi-receipt-cutoff me-1"></i>Profit & Loss Statement</div>

            <div class="px-3 pt-3 pb-1">
                <div class="text-uppercase fw-bold" style="font-size:.72rem;color:#0ea5e9;letter-spacing:.05em">Income</div>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-calendar2-check me-2 text-muted"></i>Service Revenue</span>
                <span id="pnl-service-revenue">{{ auth()->user()->currency }}{{ number_format($serviceRevenue, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-box-seam me-2 text-muted"></i>Appointment Product Revenue</span>
                <span id="pnl-product-revenue">{{ auth()->user()->currency }}{{ number_format($productRevenue, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-cart3 me-2 text-muted"></i>POS Sales (Paid)</span>
                <span id="pnl-pos-sales">{{ auth()->user()->currency }}{{ number_format($posSales, 2) }}</span>
            </div>
            <div class="pnl-row total-row">
                <span>Total Income</span>
                <span id="pnl-total-income" style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($totalIncome, 2) }}</span>
            </div>

            <div class="px-3 pt-3 pb-1">
                <div class="text-uppercase fw-bold" style="font-size:.72rem;color:#ef4444;letter-spacing:.05em">Expenses</div>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-wallet2 me-2 text-muted"></i>Business Expenses</span>
                <span class="text-danger" id="pnl-biz-expenses">{{ auth()->user()->currency }}{{ number_format($businessExpenses, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-people me-2 text-muted"></i>Salary Costs</span>
                <span class="text-danger" id="pnl-salary">{{ auth()->user()->currency }}{{ number_format($salaryCosts, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-person-badge me-2 text-muted"></i>Doctor Shares</span>
                <span class="text-danger" id="pnl-doctor-shares">{{ auth()->user()->currency }}{{ number_format($doctorShares, 2) }}</span>
            </div>
            <div class="pnl-row">
                <span><i class="bi bi-box me-2 text-muted"></i>Product COGS (Appt)</span>
                <span class="text-danger" id="pnl-cogs">{{ auth()->user()->currency }}{{ number_format($productCogs, 2) }}</span>
            </div>
            <div class="pnl-row total-row">
                <span>Total Expenses</span>
                <span class="text-danger" id="pnl-total-expenses">{{ auth()->user()->currency }}{{ number_format($totalExpenses, 2) }}</span>
            </div>

            <div class="pnl-row {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}" id="pnl-net-row">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Net Profit / Loss</span>
                <span id="pnl-net-profit">{{ auth()->user()->currency }}{{ number_format($netProfit, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Trend Chart --}}
    <div class="col-lg-7">
        <div class="pnl-panel h-100">
            <div class="pnl-panel-head"><i class="bi bi-graph-up me-1"></i>Monthly Income vs Expense vs Profit</div>
            <div class="p-3" id="chart-wrap">
                @if(count($trendMonths) > 0)
                <canvas id="trendChart" height="{{ count($trendMonths) == 1 ? '120' : '180' }}"></canvas>
                @else
                <p class="text-muted text-center py-5" id="chart-empty">No data for the selected period.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Income & Expense Breakdown Bars ── --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="pnl-panel">
            <div class="pnl-panel-head"><i class="bi bi-pie-chart me-1"></i>Income Breakdown</div>
            <div class="p-4" id="income-breakdown">
                @php
                    $incomeItems = [
                        ['id'=>'bar-service','label'=>'Service Revenue',  'value'=>$serviceRevenue, 'color'=>'#0ea5e9'],
                        ['id'=>'bar-product','label'=>'Product Revenue',  'value'=>$productRevenue, 'color'=>'#8b5cf6'],
                        ['id'=>'bar-pos',    'label'=>'POS Sales',        'value'=>$posSales,       'color'=>'#B1083C'],
                    ];
                @endphp
                @foreach($incomeItems as $item)
                @php $pct = $totalIncome > 0 ? round($item['value'] / $totalIncome * 100, 1) : 0; @endphp
                <div class="mb-3" id="{{ $item['id'] }}-wrap">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem">
                        <span class="fw-semibold">{{ $item['label'] }}</span>
                        <span>
                            <span id="{{ $item['id'] }}-val">{{ auth()->user()->currency }}{{ number_format($item['value'], 0) }}</span>
                            &nbsp;<span class="text-muted" id="{{ $item['id'] }}-pct">({{ $pct }}%)</span>
                        </span>
                    </div>
                    <div class="bg-light rounded" style="height:8px">
                        <div class="rounded bar-fill" id="{{ $item['id'] }}-bar" style="height:8px;width:{{ $pct }}%;background:{{ $item['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="pnl-panel">
            <div class="pnl-panel-head"><i class="bi bi-pie-chart-fill me-1"></i>Expense Breakdown</div>
            <div class="p-4">
                @php
                    $expenseItems = [
                        ['id'=>'bar-biz',    'label'=>'Business Expenses','value'=>$businessExpenses,'color'=>'#ef4444'],
                        ['id'=>'bar-salary', 'label'=>'Salary Costs',     'value'=>$salaryCosts,     'color'=>'#f97316'],
                        ['id'=>'bar-doc',    'label'=>'Doctor Shares',     'value'=>$doctorShares,    'color'=>'#f59e0b'],
                        ['id'=>'bar-pcogs',  'label'=>'Product COGS',      'value'=>$productCogs,     'color'=>'#6b7280'],
                    ];
                @endphp
                @foreach($expenseItems as $item)
                @php $pct = $totalExpenses > 0 ? round($item['value'] / $totalExpenses * 100, 1) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem">
                        <span class="fw-semibold">{{ $item['label'] }}</span>
                        <span>
                            <span id="{{ $item['id'] }}-val">{{ auth()->user()->currency }}{{ number_format($item['value'], 0) }}</span>
                            &nbsp;<span class="text-muted" id="{{ $item['id'] }}-pct">({{ $pct }}%)</span>
                        </span>
                    </div>
                    <div class="bg-light rounded" style="height:8px">
                        <div class="rounded bar-fill" id="{{ $item['id'] }}-bar" style="height:8px;width:{{ $pct }}%;background:{{ $item['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Monthly Trend Table ── --}}
<div id="monthly-table-wrap">
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
            <tbody id="monthly-table-body">
                @foreach($trendMonths as $i => $month)
                @php $mI=$trendIncome[$i]??0; $mE=$trendExpense[$i]??0; $mP=$trendProfit[$i]??0; @endphp
                <tr>
                    <td class="ps-3 fw-semibold">{{ $month }}</td>
                    <td class="text-end" style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($mI,0) }}</td>
                    <td class="text-end text-danger">{{ auth()->user()->currency }}{{ number_format($mE,0) }}</td>
                    <td class="text-end pe-3 fw-semibold {{ $mP>=0?'text-success':'text-danger' }}">
                        {{ $mP>=0?'+':'' }}{{ auth()->user()->currency }}{{ number_format($mP,0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <td class="ps-3 fw-bold">Total</td>
                    <td class="text-end fw-bold" style="color:#0ea5e9">{{ auth()->user()->currency }}{{ number_format($totalIncome,0) }}</td>
                    <td class="text-end fw-bold text-danger">{{ auth()->user()->currency }}{{ number_format($totalExpenses,0) }}</td>
                    <td class="text-end pe-3 fw-bold {{ $netProfit>=0?'text-success':'text-danger' }}">
                        {{ $netProfit>=0?'+':'' }}{{ auth()->user()->currency }}{{ number_format($netProfit,0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const CURRENCY = '{{ auth()->user()->currency ?? "" }}';
    const AJAX_URL = '{{ route("reports.summary") }}';
    let trendChart = null;

    // ── Helpers ──────────────────────────────────────────────────────────
    function fmt(v, d = 0)   { return CURRENCY + Number(v).toLocaleString(undefined, {minimumFractionDigits:d, maximumFractionDigits:d}); }
    function pct(v, total)   { return total > 0 ? (v / total * 100).toFixed(1) : '0.0'; }
    function fmtDate(d)      { return d.toISOString().split('T')[0]; }

    // ── Date range calculator ────────────────────────────────────────────
    function calcRange(preset) {
        const now   = new Date();
        const y     = now.getFullYear();
        const m     = now.getMonth();
        const d     = now.getDate();
        const dow   = now.getDay(); // 0=Sun

        switch (preset) {
            case 'today':
                return { from: fmtDate(now), to: fmtDate(now) };

            case 'yesterday': {
                const y1 = new Date(now); y1.setDate(d - 1);
                return { from: fmtDate(y1), to: fmtDate(y1) };
            }

            case 'this_week': {
                const diff = dow === 0 ? 6 : dow - 1; // Mon=0 offset
                const mon  = new Date(now); mon.setDate(d - diff);
                return { from: fmtDate(mon), to: fmtDate(now) };
            }

            case 'last_week': {
                const diff   = dow === 0 ? 6 : dow - 1;
                const thisMon = new Date(now); thisMon.setDate(d - diff);
                const lastMon = new Date(thisMon); lastMon.setDate(thisMon.getDate() - 7);
                const lastSun = new Date(lastMon); lastSun.setDate(lastMon.getDate() + 6);
                return { from: fmtDate(lastMon), to: fmtDate(lastSun) };
            }

            case 'this_month':
                return { from: `${y}-${String(m+1).padStart(2,'0')}-01`, to: fmtDate(now) };

            case 'this_year':
                return { from: `${y}-01-01`, to: fmtDate(now) };

            case 'last_year':
                return { from: `${y-1}-01-01`, to: `${y-1}-12-31` };

            default: return null;
        }
    }

    // ── Show/hide custom inputs ──────────────────────────────────────────
    const preset  = document.getElementById('date-preset');
    const custWrap = document.getElementById('custom-range-inputs');
    const applyWrap = document.getElementById('apply-btn-wrap');

    function toggleCustom() {
        const isCustom = preset.value === 'custom';
        custWrap.classList.toggle('show', isCustom);
        applyWrap.style.display = isCustom ? '' : 'none';
    }
    toggleCustom();

    preset.addEventListener('change', function () {
        toggleCustom();
        if (this.value !== 'custom') fetchData();
    });

    // ── Apply button for custom range ────────────────────────────────────
    document.getElementById('apply-filter').addEventListener('click', fetchData);

    // ── Clinic/doctor dropdowns auto-apply ───────────────────────────────
    ['filter-clinic','filter-doctor'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', fetchData);
    });

    // ── AJAX fetch ───────────────────────────────────────────────────────
    function fetchData() {
        let from, to;

        if (preset.value === 'custom') {
            from = document.getElementById('filter-from').value;
            to   = document.getElementById('filter-to').value;
            if (!from || !to) return;
        } else {
            const range = calcRange(preset.value);
            from = range.from;
            to   = range.to;
        }

        const clinicEl = document.getElementById('filter-clinic');
        const doctorEl = document.getElementById('filter-doctor');
        const params   = new URLSearchParams({ from, to });
        if (clinicEl) params.append('clinic_id', clinicEl.value);
        if (doctorEl) params.append('doctor_id', doctorEl.value);

        document.getElementById('summary-loading').classList.add('show');

        fetch(`${AJAX_URL}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => updateUI(d))
        .catch(e => console.error('Summary AJAX error:', e))
        .finally(() => document.getElementById('summary-loading').classList.remove('show'));
    }

    // ── Update all DOM elements from JSON ────────────────────────────────
    function updateUI(d) {
        const cur = d.currency;
        const f0  = v => cur + Number(v).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:0});
        const f2  = v => cur + Number(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
        const p   = (v, total) => total > 0 ? (v / total * 100).toFixed(1) : '0.0';

        // KPI cards
        document.getElementById('kpi-service-revenue').textContent = f0(d.serviceRevenue);
        document.getElementById('kpi-product-revenue').textContent = f0(d.productRevenue);
        document.getElementById('kpi-pos-sales').textContent       = f0(d.posSales);
        document.getElementById('kpi-net-profit').textContent      = f0(d.netProfit);
        const npCard = document.getElementById('kpi-net-profit-card');
        npCard.style.background = d.netProfit >= 0
            ? 'linear-gradient(135deg,#10b981,#34d399)'
            : 'linear-gradient(135deg,#ef4444,#f87171)';

        // P&L rows
        document.getElementById('pnl-service-revenue').textContent = f2(d.serviceRevenue);
        document.getElementById('pnl-product-revenue').textContent = f2(d.productRevenue);
        document.getElementById('pnl-pos-sales').textContent       = f2(d.posSales);
        document.getElementById('pnl-total-income').textContent    = f2(d.totalIncome);
        document.getElementById('pnl-biz-expenses').textContent    = f2(d.businessExpenses);
        document.getElementById('pnl-salary').textContent          = f2(d.salaryCosts);
        document.getElementById('pnl-doctor-shares').textContent   = f2(d.doctorShares);
        document.getElementById('pnl-cogs').textContent            = f2(d.productCogs);
        document.getElementById('pnl-total-expenses').textContent  = f2(d.totalExpenses);
        document.getElementById('pnl-net-profit').textContent      = f2(d.netProfit);
        const netRow = document.getElementById('pnl-net-row');
        netRow.className = 'pnl-row ' + (d.netProfit >= 0 ? 'profit-positive' : 'profit-negative');

        // Income breakdown bars
        const incomeMap = {
            'bar-service': d.serviceRevenue,
            'bar-product': d.productRevenue,
            'bar-pos':     d.posSales,
        };
        Object.entries(incomeMap).forEach(([id, val]) => {
            const pc = p(val, d.totalIncome);
            document.getElementById(id+'-val').textContent = f0(val);
            document.getElementById(id+'-pct').textContent = `(${pc}%)`;
            document.getElementById(id+'-bar').style.width = pc + '%';
        });

        // Expense breakdown bars
        const expMap = {
            'bar-biz':    d.businessExpenses,
            'bar-salary': d.salaryCosts,
            'bar-doc':    d.doctorShares,
            'bar-pcogs':  d.productCogs,
        };
        Object.entries(expMap).forEach(([id, val]) => {
            const pc = p(val, d.totalExpenses);
            document.getElementById(id+'-val').textContent = f0(val);
            document.getElementById(id+'-pct').textContent = `(${pc}%)`;
            document.getElementById(id+'-bar').style.width = pc + '%';
        });

        // Chart update
        updateChart(d.trendMonths, d.trendIncome, d.trendExpense, d.trendProfit, cur);

        // Monthly table
        updateMonthlyTable(d.trendMonths, d.trendIncome, d.trendExpense, d.trendProfit, cur);
    }

    // ── Chart init / update ──────────────────────────────────────────────
    function buildDatasets(income, expense, profit) {
        return [
            { label:'Income',    data:income,  backgroundColor:'rgba(14,165,233,.7)', borderColor:'#0ea5e9', borderWidth:1, borderRadius:4, yAxisID:'y' },
            { label:'Expenses',  data:expense, backgroundColor:'rgba(239,68,68,.7)',  borderColor:'#ef4444', borderWidth:1, borderRadius:4, yAxisID:'y' },
            { label:'Net Profit',data:profit,  type:'line', borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.15)', tension:.3, borderWidth:2, pointBackgroundColor:profit.map(v=>v>=0?'#10b981':'#ef4444'), yAxisID:'y' },
        ];
    }

    function updateChart(months, income, expense, profit, cur) {
        const wrap = document.getElementById('chart-wrap');

        if (!months.length) {
            wrap.innerHTML = '<p class="text-muted text-center py-5">No data for the selected period.</p>';
            trendChart = null;
            return;
        }

        if (trendChart) {
            trendChart.data.labels            = months;
            trendChart.data.datasets[0].data  = income;
            trendChart.data.datasets[1].data  = expense;
            trendChart.data.datasets[2].data  = profit;
            trendChart.data.datasets[2].pointBackgroundColor = profit.map(v=>v>=0?'#10b981':'#ef4444');
            trendChart.options.scales.y.ticks.callback = v => cur + v.toLocaleString();
            trendChart.options.plugins.tooltip.callbacks.label = ctx =>
                ' ' + ctx.dataset.label + ': ' + cur + ctx.parsed.y.toLocaleString(undefined,{minimumFractionDigits:0});
            trendChart.update();
        } else {
            wrap.innerHTML = `<canvas id="trendChart" height="${months.length===1?'120':'180'}"></canvas>`;
            trendChart = new Chart(document.getElementById('trendChart'), {
                type: 'bar',
                data: { labels:months, datasets:buildDatasets(income,expense,profit) },
                options: {
                    responsive:true,
                    interaction:{ mode:'index', intersect:false },
                    plugins:{
                        legend:{ position:'top', labels:{ font:{size:11} } },
                        tooltip:{ callbacks:{ label: ctx => ' '+ctx.dataset.label+': '+cur+ctx.parsed.y.toLocaleString(undefined,{minimumFractionDigits:0}) } }
                    },
                    scales:{ y:{ beginAtZero:true, ticks:{ callback: v => cur+v.toLocaleString() } } }
                }
            });
        }
    }

    // ── Monthly table update ─────────────────────────────────────────────
    function updateMonthlyTable(months, income, expense, profit, cur) {
        const f0 = v => cur + Number(v).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:0});
        const totalI = income.reduce((a,b)=>a+b,0);
        const totalE = expense.reduce((a,b)=>a+b,0);
        const totalP = profit.reduce((a,b)=>a+b,0);
        const tableWrap = document.getElementById('monthly-table-wrap');

        if (months.length <= 1) { tableWrap.innerHTML = ''; return; }

        let rows = months.map((m,i) => {
            const mI=income[i]??0, mE=expense[i]??0, mP=profit[i]??0;
            return `<tr>
                <td class="ps-3 fw-semibold">${m}</td>
                <td class="text-end" style="color:#0ea5e9">${f0(mI)}</td>
                <td class="text-end text-danger">${f0(mE)}</td>
                <td class="text-end pe-3 fw-semibold ${mP>=0?'text-success':'text-danger'}">${mP>=0?'+':''}${f0(mP)}</td>
            </tr>`;
        }).join('');

        tableWrap.innerHTML = `
        <div class="pnl-panel mb-4">
            <div class="pnl-panel-head"><i class="bi bi-table me-1"></i>Monthly Breakdown</div>
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0" style="font-size:.875rem">
                    <thead class="table-light">
                        <tr><th class="ps-3">Month</th><th class="text-end">Income</th><th class="text-end">Expenses</th><th class="text-end pe-3">Net Profit</th></tr>
                    </thead>
                    <tbody>${rows}</tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td class="ps-3 fw-bold">Total</td>
                            <td class="text-end fw-bold" style="color:#0ea5e9">${f0(totalI)}</td>
                            <td class="text-end fw-bold text-danger">${f0(totalE)}</td>
                            <td class="text-end pe-3 fw-bold ${totalP>=0?'text-success':'text-danger'}">${totalP>=0?'+':''}${f0(totalP)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>`;
    }

    // ── Init chart on page load ───────────────────────────────────────────
    const initMonths  = @json($trendMonths);
    const initIncome  = @json($trendIncome);
    const initExpense = @json($trendExpense);
    const initProfit  = @json($trendProfit);

    if (initMonths.length) {
        trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: { labels:initMonths, datasets:buildDatasets(initIncome,initExpense,initProfit) },
            options: {
                responsive:true,
                interaction:{ mode:'index', intersect:false },
                plugins:{
                    legend:{ position:'top', labels:{ font:{size:11} } },
                    tooltip:{ callbacks:{ label: ctx => ' '+ctx.dataset.label+': '+CURRENCY+ctx.parsed.y.toLocaleString(undefined,{minimumFractionDigits:0}) } }
                },
                scales:{ y:{ beginAtZero:true, ticks:{ callback: v => CURRENCY+v.toLocaleString() } } }
            }
        });
    }
})();
</script>
@endsection
