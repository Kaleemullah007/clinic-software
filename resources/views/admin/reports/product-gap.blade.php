@extends('layouts.admin')
@section('title', 'Product Gap Report')

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
    .channel-radio-group { display:flex; gap:8px; flex-wrap:wrap; }
    .channel-radio-label {
        display:flex; align-items:center; gap:8px; cursor:pointer;
        padding:8px 18px; border-radius:8px; border:2px solid #e2e8f0;
        font-weight:600; font-size:.875rem; background:#fff;
        transition:border-color .15s, background .15s;
        user-select:none;
    }
    .channel-radio-label input { display:none; }
    .channel-radio-label.selected-both   { border-color:#B1083C; background:#fff5f7; color:#B1083C; }
    .channel-radio-label.selected-appt   { border-color:#6366f1; background:#f5f3ff; color:#6366f1; }
    .channel-radio-label.selected-pos    { border-color:#0ea5e9; background:#f0f9ff; color:#0ea5e9; }
    .dead-badge-never    { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; white-space:nowrap; }
    .dead-badge-dormant  { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; white-space:nowrap; }
    .dead-badge-period   { background:#ffedd5; color:#9a3412; border:1px solid #fed7aa; font-size:.72rem; padding:2px 8px; border-radius:20px; font-weight:600; white-space:nowrap; }
    .dead-row            { background:#fff8f8; }
    .print-header-only   { display: none; }
    @media print {
        canvas { max-height: 280px !important; }
        .rpt-panel { box-shadow: none !important; border: 1px solid #e5e5e5; }
        .rpt-stat-card { box-shadow: none !important; border: 1px solid #ddd; break-inside: avoid; }
        .rpt-table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .channel-radio-group { display: none !important; }
        .channel-section.d-none { display: block !important; }
    }
</style>

@php
    $totalProducts  = $allProducts->count();
    $apptActiveCount = $apptIds->count();
    $posActiveCount  = $posIds->count();
    // top 10 for charts
    $apptTop  = $apptActive->take(10);
    $posTop   = $posActive->take(10);
@endphp

<div class="container-fluid">

    {{-- Print-only header --}}
    <div class="print-header-only">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <strong style="font-size:18px;color:#B1083C;">RKTech</strong>
            <span style="font-size:15px;font-weight:600;color:#1a1a2e;">— Product Gap Report</span>
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

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="row pt-3 mx-1 align-items-center no-print">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-box-seam me-2" style="color:#B1083C"></i>Product Gap Report
            </h4>
        </div>
        <hr class="my-3">
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
    <div class="row mx-1 mb-3 g-2 align-items-end no-print">
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

    {{-- ── Radio Buttons ────────────────────────────────────────────────── --}}
    <div class="row mx-1 mb-4">
        <div class="col-12">
            <div class="channel-radio-group" id="channelGroup">
                <label class="channel-radio-label selected-both" id="lbl-both">
                    <input type="radio" name="channel" value="both" checked>
                    <i class="bi bi-x-octagon-fill"></i> Both Channels Dead
                </label>
                <label class="channel-radio-label" id="lbl-appt">
                    <input type="radio" name="channel" value="appt">
                    <i class="bi bi-calendar2-x"></i> Dead in Appointments
                </label>
                <label class="channel-radio-label" id="lbl-pos">
                    <input type="radio" name="channel" value="pos">
                    <i class="bi bi-cart-x"></i> Dead in POS
                </label>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: BOTH CHANNELS
    ══════════════════════════════════════════════════════════════════ --}}
    <div id="section-both" class="channel-section">

        {{-- Stat Cards --}}
        <div class="row mx-1 g-3 mb-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="bi bi-box-seam"></i></div>
                    <div class="rpt-stat-num">{{ $totalProducts }}</div>
                    <div class="rpt-stat-lbl">Total Products in System</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-x-octagon"></i></div>
                    <div class="rpt-stat-num">{{ $deadBoth->count() }}</div>
                    <div class="rpt-stat-lbl">Dead in Both Channels</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-calendar2-check"></i></div>
                    <div class="rpt-stat-num">{{ $apptActiveCount }}</div>
                    <div class="rpt-stat-lbl">Active in Appointments</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9"><i class="bi bi-cart-check"></i></div>
                    <div class="rpt-stat-num">{{ $posActiveCount }}</div>
                    <div class="rpt-stat-lbl">Active in POS</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row mx-1 g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="rpt-panel">
                    <div class="rpt-panel-head"><i class="bi bi-calendar2-check me-2" style="color:#6366f1"></i>Top Products — Appointments</div>
                    <div class="rpt-panel-body">
                        @if($apptTop->isEmpty())
                            <p class="text-muted text-center py-3">No appointment data.</p>
                        @else
                            <canvas id="chart-both-appt" height="120"></canvas>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="rpt-panel">
                    <div class="rpt-panel-head"><i class="bi bi-cart-check me-2" style="color:#0ea5e9"></i>Top Products — POS Sales</div>
                    <div class="rpt-panel-body">
                        @if($posTop->isEmpty())
                            <p class="text-muted text-center py-3">No POS data.</p>
                        @else
                            <canvas id="chart-both-pos" height="120"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Dead in Both Table --}}
        @include('admin.reports._product-dead-table', [
            'tableId'     => 'tbl-both',
            'deadProducts'=> $deadBoth,
            'everAppt'    => $everAppt,
            'everPos'     => $everPos,
            'apptIds'     => $apptIds,
            'posIds'      => $posIds,
            'showAppt'    => true,
            'showPos'     => true,
            'emptyMsg'    => 'All products are active in at least one channel!',
            'title'       => 'Products Dead in Both Channels',
            'accentColor' => '#ef4444',
        ])

    </div>{{-- /section-both --}}

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: APPOINTMENTS ONLY
    ══════════════════════════════════════════════════════════════════ --}}
    <div id="section-appt" class="channel-section d-none">

        {{-- Stat Cards --}}
        <div class="row mx-1 g-3 mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="bi bi-box-seam"></i></div>
                    <div class="rpt-stat-num">{{ $totalProducts }}</div>
                    <div class="rpt-stat-lbl">Total Products in System</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-calendar2-check"></i></div>
                    <div class="rpt-stat-num">{{ $apptActiveCount }}</div>
                    <div class="rpt-stat-lbl">Active in Appointments</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="bi bi-calendar2-x"></i></div>
                    <div class="rpt-stat-num">{{ $deadAppt->count() }}</div>
                    <div class="rpt-stat-lbl">Not Used in Appointments</div>
                </div>
            </div>
        </div>

        {{-- Chart --}}
        <div class="row mx-1 g-4 mb-4">
            <div class="col-12">
                <div class="rpt-panel">
                    <div class="rpt-panel-head"><i class="bi bi-bar-chart-horizontal me-2" style="color:#6366f1"></i>Top Products by Appointment Usage</div>
                    <div class="rpt-panel-body">
                        @if($apptTop->isEmpty())
                            <p class="text-muted text-center py-3">No appointment data for this period.</p>
                        @else
                            <canvas id="chart-appt" height="80"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Dead in Appt Table --}}
        @include('admin.reports._product-dead-table', [
            'tableId'     => 'tbl-appt',
            'deadProducts'=> $deadAppt,
            'everAppt'    => $everAppt,
            'everPos'     => $everPos,
            'apptIds'     => $apptIds,
            'posIds'      => $posIds,
            'showAppt'    => true,
            'showPos'     => false,
            'emptyMsg'    => 'All products were used in appointments this period!',
            'title'       => 'Products Not Used in Appointments',
            'accentColor' => '#6366f1',
        ])

    </div>{{-- /section-appt --}}

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: POS ONLY
    ══════════════════════════════════════════════════════════════════ --}}
    <div id="section-pos" class="channel-section d-none">

        {{-- Stat Cards --}}
        <div class="row mx-1 g-3 mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="bi bi-box-seam"></i></div>
                    <div class="rpt-stat-num">{{ $totalProducts }}</div>
                    <div class="rpt-stat-lbl">Total Products in System</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9"><i class="bi bi-cart-check"></i></div>
                    <div class="rpt-stat-num">{{ $posActiveCount }}</div>
                    <div class="rpt-stat-lbl">Active in POS</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="rpt-stat-card">
                    <div class="rpt-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9"><i class="bi bi-cart-x"></i></div>
                    <div class="rpt-stat-num">{{ $deadPos->count() }}</div>
                    <div class="rpt-stat-lbl">Not Sold in POS</div>
                </div>
            </div>
        </div>

        {{-- Chart --}}
        <div class="row mx-1 g-4 mb-4">
            <div class="col-12">
                <div class="rpt-panel">
                    <div class="rpt-panel-head"><i class="bi bi-bar-chart-horizontal me-2" style="color:#0ea5e9"></i>Top Products by POS Sales</div>
                    <div class="rpt-panel-body">
                        @if($posTop->isEmpty())
                            <p class="text-muted text-center py-3">No POS data for this period.</p>
                        @else
                            <canvas id="chart-pos" height="80"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Dead in POS Table --}}
        @include('admin.reports._product-dead-table', [
            'tableId'     => 'tbl-pos',
            'deadProducts'=> $deadPos,
            'everAppt'    => $everAppt,
            'everPos'     => $everPos,
            'apptIds'     => $apptIds,
            'posIds'      => $posIds,
            'showAppt'    => false,
            'showPos'     => true,
            'emptyMsg'    => 'All products were sold through POS this period!',
            'title'       => 'Products Not Sold in POS',
            'accentColor' => '#0ea5e9',
        ])

    </div>{{-- /section-pos --}}

</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Pre-computed chart data from PHP ──────────────────────────────────────
const apptLabels   = @json($apptTop->pluck('product_name'));
const apptBookings = @json($apptTop->pluck('usage_count'));
const posLabels    = @json($posTop->pluck('product_name'));
const posQty       = @json($posTop->pluck('qty_sold'));

// ── Lazy chart initialization ─────────────────────────────────────────────
const chartInited = { both: false, appt: false, pos: false };

function initCharts(channel) {
    if (chartInited[channel]) return;
    chartInited[channel] = true;

    if (channel === 'both') {
        if (document.getElementById('chart-both-appt')) {
            new Chart(document.getElementById('chart-both-appt'), {
                type: 'bar',
                data: {
                    labels: apptLabels,
                    datasets: [{ label: 'Used in Appts', data: apptBookings,
                        backgroundColor: 'rgba(99,102,241,0.75)', borderRadius: 5 }]
                },
                options: { indexAxis:'y', plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true } } }
            });
        }
        if (document.getElementById('chart-both-pos')) {
            new Chart(document.getElementById('chart-both-pos'), {
                type: 'bar',
                data: {
                    labels: posLabels,
                    datasets: [{ label: 'Qty Sold', data: posQty,
                        backgroundColor: 'rgba(14,165,233,0.75)', borderRadius: 5 }]
                },
                options: { indexAxis:'y', plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true } } }
            });
        }
    }

    if (channel === 'appt' && document.getElementById('chart-appt')) {
        new Chart(document.getElementById('chart-appt'), {
            type: 'bar',
            data: {
                labels: apptLabels,
                datasets: [{ label: 'Used in Appointments', data: apptBookings,
                    backgroundColor: 'rgba(99,102,241,0.75)', borderRadius: 5 }]
            },
            options: { indexAxis:'y', plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true } } }
        });
    }

    if (channel === 'pos' && document.getElementById('chart-pos')) {
        new Chart(document.getElementById('chart-pos'), {
            type: 'bar',
            data: {
                labels: posLabels,
                datasets: [{ label: 'Qty Sold in POS', data: posQty,
                    backgroundColor: 'rgba(14,165,233,0.75)', borderRadius: 5 }]
            },
            options: { indexAxis:'y', plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true } } }
        });
    }
}

// Initialize default section on load
initCharts('both');

// ── Radio button switching ────────────────────────────────────────────────
const labelMap = { both: 'lbl-both', appt: 'lbl-appt', pos: 'lbl-pos' };
const classMap = { both: 'selected-both', appt: 'selected-appt', pos: 'selected-pos' };

document.querySelectorAll('input[name="channel"]').forEach(radio => {
    radio.addEventListener('change', function () {
        // Hide all sections
        document.querySelectorAll('.channel-section').forEach(s => s.classList.add('d-none'));
        // Remove all selected classes from labels
        Object.keys(labelMap).forEach(k => {
            const lbl = document.getElementById(labelMap[k]);
            lbl.classList.remove('selected-both', 'selected-appt', 'selected-pos');
        });
        // Show selected section & apply active style
        document.getElementById('section-' + this.value).classList.remove('d-none');
        document.getElementById(labelMap[this.value]).classList.add(classMap[this.value]);
        // Lazy-init chart for this section
        initCharts(this.value);
    });
});

// ── Apply filter ──────────────────────────────────────────────────────────
document.getElementById('applyFilter').addEventListener('click', function () {
    const y = document.getElementById('filterYear').value;
    const m = document.getElementById('filterMonth').value;
    const c = document.getElementById('filterClinic').value;
    const d = document.getElementById('filterDoctor').value;
    const ch = document.querySelector('input[name="channel"]:checked').value;
    let url = '{{ route("reports.product-gap") }}?year=' + y + '&channel=' + ch;
    if (m) url += '&month=' + m;
    if (c) url += '&clinic_id=' + c;
    if (d) url += '&doctor_id=' + d;
    window.location.href = url;
});

// ── Restore radio state from URL on load ─────────────────────────────────
(function () {
    const params = new URLSearchParams(window.location.search);
    const ch = params.get('channel') || 'both';
    const radio = document.querySelector('input[name="channel"][value="' + ch + '"]');
    if (radio && ch !== 'both') {
        radio.checked = true;
        radio.dispatchEvent(new Event('change'));
    }
})();
</script>
@endsection
