@extends('layouts.admin')
@section('title', 'POS Report')

{{-- styles injected inside content section below --}}

@section('content')
<style>
    .stat-card { border-radius:10px; padding:18px 20px; color:#fff; }
    .chart-card { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:20px; }
</style>
<div class="page-breadcrumb d-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">POS Report</div>
    <div class="ps-3">
        <nav><ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">POS Orders</a></li>
            <li class="breadcrumb-item active">Report</li>
        </ol></nav>
    </div>
</div>

{{-- ── Filter bar ── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('pos.report') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">From</label>
                    <input type="date" name="date_from" value="{{ $from }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">To</label>
                    <input type="date" name="date_to" value="{{ $to }}" class="form-control form-control-sm">
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Clinic</label>
                    <select name="clinic_id" class="form-select form-select-sm">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ request('clinic_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm text-white w-100" style="background:linear-gradient(90deg,#B1083C,#d13729);border:none">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Summary cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#B1083C,#d13729)">
            <div class="small opacity-75">Total Orders</div>
            <div class="fs-3 fw-bold">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8)">
            <div class="small opacity-75">Gross Sales</div>
            <div class="fs-4 fw-bold">{{ auth()->user()->currency }}{{ number_format($grossSales, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
            <div class="small opacity-75">Total Discount</div>
            <div class="fs-4 fw-bold">{{ auth()->user()->currency }}{{ number_format($totalDiscount, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#34d399)">
            <div class="small opacity-75">Net Revenue</div>
            <div class="fs-4 fw-bold">{{ auth()->user()->currency }}{{ number_format($netRevenue, 0) }}</div>
        </div>
    </div>
</div>

{{-- ── Charts ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="chart-card">
            <h6 class="fw-semibold mb-3" style="color:#B1083C"><i class="bi bi-graph-up me-1"></i>Daily Revenue ({{ $from }} to {{ $to }})</h6>
            <canvas id="chartDaily" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <h6 class="fw-semibold mb-3" style="color:#B1083C"><i class="bi bi-pie-chart me-1"></i>Sales by Category</h6>
            <canvas id="chartCategory" height="200"></canvas>
            @if(!$salesByCategory->count())
            <p class="text-muted text-center small mt-3">No data for this period.</p>
            @endif
        </div>
    </div>
</div>

{{-- ── Product breakdown ── --}}
<div class="card mb-4">
    <div class="card-header fw-semibold" style="background:linear-gradient(90deg,#B1083C,#d13729);color:#fff">
        <i class="bi bi-table me-1"></i>Product Breakdown
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0" style="font-size:.875rem">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Product</th>
                        <th>Category</th>
                        <th class="text-right">Units Sold</th>
                        <th class="text-right pe-3">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productBreakdown as $row)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $row->product_name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $row->category ?? '—' }}</span></td>
                        <td class="text-right">{{ $row->total_qty }}</td>
                        <td class="text-right pe-3 fw-semibold" style="color:#B1083C">{{ auth()->user()->currency }}{{ number_format($row->total_revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No product data for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Orders list ── --}}
<div class="card">
    <div class="card-header fw-semibold" style="background:linear-gradient(90deg,#B1083C,#d13729);color:#fff">
        <i class="bi bi-receipt me-1"></i>Orders List
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0" style="font-size:.875rem">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Order #</th>
                        <th>Patient</th>
                        @if(auth()->user()->isSuperAdmin())<th>Clinic</th>@endif
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Grand Total</th>
                        <th>Status</th>
                        <th>By</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordersList as $order)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->patient?->name ?? '—' }}</td>
                        @if(auth()->user()->isSuperAdmin())<td>{{ $order->clinic?->name ?? '—' }}</td>@endif
                        <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                        <td class="text-right text-danger">{{ $order->discount > 0 ? '— ' . number_format($order->discount, 2) : '—' }}</td>
                        <td class="text-right">{{ $order->tax_amount > 0 ? number_format($order->tax_amount, 2) : '—' }}</td>
                        <td class="text-right fw-semibold" style="color:#B1083C">{{ auth()->user()->currency }}{{ number_format($order->grand_total, 2) }}</td>
                        <td>
                            @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                            @else
                            <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $order->creator?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('pos.show', $order->id) }}" class="btn btn-sm btn-outline-info py-0 px-1" title="Receipt">
                                <i class="bi bi-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->user()->isSuperAdmin() ? 11 : 10 }}" class="text-center text-muted py-4">No orders found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const brandRed = '#B1083C';
    const currency = '{{ auth()->user()->currency ?? "PKR " }}';

    // ── Daily revenue chart ──────────────────────────────────────────
    const dailyData  = @json($dailySales);
    const dailyCtx   = document.getElementById('chartDaily')?.getContext('2d');
    if (dailyCtx && dailyData.length) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels:   dailyData.map(d => d.day),
                datasets: [
                    {
                        label:           'Revenue',
                        data:            dailyData.map(d => parseFloat(d.revenue || 0)),
                        backgroundColor: 'rgba(177,8,60,.7)',
                        borderColor:     brandRed,
                        borderWidth:     1,
                        yAxisID:         'y',
                    },
                    {
                        label:     'Orders',
                        data:      dailyData.map(d => parseInt(d.order_count || 0)),
                        type:      'line',
                        borderColor: '#0ea5e9',
                        backgroundColor:'rgba(14,165,233,.1)',
                        tension: .3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { beginAtZero:true, ticks:{ callback: v => currency + v.toLocaleString() } },
                    y1: { beginAtZero:true, position:'right', grid:{ drawOnChartArea:false }, ticks:{ precision:0 } }
                }
            }
        });
    } else if (dailyCtx) {
        dailyCtx.canvas.parentElement.innerHTML += '<p class="text-muted text-center small mt-3">No data for this period.</p>';
    }

    // ── Category pie chart ───────────────────────────────────────────
    const catData = @json($salesByCategory);
    const catCtx  = document.getElementById('chartCategory')?.getContext('2d');
    const palette = ['#B1083C','#d13729','#e85d2d','#f59e0b','#10b981','#0ea5e9','#8b5cf6','#ec4899','#6b7280','#1f2937'];
    if (catCtx && catData.length) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels:   catData.map(d => d.category),
                datasets: [{
                    data:            catData.map(d => parseFloat(d.total || 0)),
                    backgroundColor: palette.slice(0, catData.length),
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + currency + ctx.parsed.toLocaleString(undefined, {minimumFractionDigits:2})
                        }
                    }
                }
            }
        });
    }
})();
</script>
@endsection
