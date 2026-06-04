<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $appointment->serial_number ?? $appointment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --brand: #B1083C; --brand2: #d13729; }
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

        /* ── Top action bar ────────────────────────────────────── */
        .action-bar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .action-bar .brand { font-weight: 700; font-size: 1.1rem; color: var(--brand); }
        .btn-brand { background: linear-gradient(90deg,var(--brand),var(--brand2)); color:#fff; border:none; }
        .btn-brand:hover { background: linear-gradient(90deg,var(--brand2),var(--brand)); color:#fff; }
        .btn-wa { background:#25d366; color:#fff; border:none; }
        .btn-wa:hover { background:#1ebe5d; color:#fff; }

        /* ── Receipt card ──────────────────────────────────────── */
        .receipt-wrap { max-width: 860px; margin: 32px auto; padding: 0 16px 60px; }
        .receipt-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.09); overflow: hidden; }

        /* Header stripe */
        .receipt-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand2) 100%);
            padding: 28px 36px;
            color: #fff;
        }
        .receipt-header h2 { font-size: 1.6rem; font-weight: 700; margin: 0; }
        .receipt-header .receipt-no { opacity: .85; font-size: .9rem; margin-top: 2px; }
        .receipt-header .logo-wrap img { max-height: 56px; border-radius: 6px; background:#fff; padding:4px 8px; }

        /* Info panels */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .info-panel { padding: 20px 28px; border-bottom: 1px solid #f0f0f0; }
        .info-panel:nth-child(odd) { border-right: 1px solid #f0f0f0; }
        .info-panel h6 { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--brand); margin-bottom: 10px; }
        .info-panel p { margin: 3px 0; font-size: .875rem; color: #374151; }
        .info-panel .label { color: #9ca3af; font-size: .78rem; }

        /* Tables */
        .section-heading {
            padding: 14px 28px 8px;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
        }
        .receipt-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .receipt-table thead th {
            padding: 10px 28px;
            background: #f8f8fb;
            color: #374151;
            font-weight: 600;
            border-bottom: 2px solid #ececec;
            white-space: nowrap;
        }
        .receipt-table tbody td { padding: 10px 28px; border-bottom: 1px solid #f4f4f4; color: #374151; }
        .receipt-table tbody tr:last-child td { border-bottom: none; }
        .receipt-table .subtotal td { background: #fafafa; font-weight: 500; color: #6b7280; }
        .receipt-table .total-row td { background: #fff7f9; font-weight: 700; color: var(--brand); border-top: 2px solid #ffe0e8; }
        .receipt-table .grand-row td {
            background: linear-gradient(90deg, var(--brand), var(--brand2));
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
            padding: 14px 28px;
        }
        .text-right { text-align: right; }

        /* Footer note */
        .receipt-footer { padding: 20px 28px; background: #fafafa; border-top: 1px solid #f0f0f0; text-align: center; color: #9ca3af; font-size: .8rem; }

        /* Print */
        @media print {
            body { background: #fff; }
            .action-bar { display: none !important; }
            .receipt-wrap { max-width: 100%; margin: 0; padding: 0; }
            .receipt-card { box-shadow: none; border-radius: 0; }
        }
        @media (max-width: 576px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-panel:nth-child(odd) { border-right: none; }
            .receipt-table thead th, .receipt-table tbody td, .receipt-table .grand-row td { padding: 8px 14px; }
            .receipt-header { padding: 20px 16px; }
        }
    </style>
</head>
<body>

    {{-- ── Action bar ───────────────────────────────────────────────────── --}}
    @php
        $waSettings  = \App\Models\Setting::whereIn('key_name', ['whatsapp_prefix','receipt_message'])
                           ->pluck('key_value','key_name');
        $waPrefix    = preg_replace('/[^0-9]/', '', $waSettings['whatsapp_prefix'] ?? '92');
        $rawPhone    = preg_replace('/[^0-9]/', '', $appointment->phone ?? '');
        if (str_starts_with($rawPhone, '0'))            { $waPhone = $waPrefix . substr($rawPhone, 1); }
        elseif (str_starts_with($rawPhone, $waPrefix))  { $waPhone = $rawPhone; }
        else                                             { $waPhone = $waPrefix . $rawPhone; }
        $receiptUrl  = route('generate-pdf', $appointment->id);
        $patientName = $appointment->customer->name ?? $appointment->name ?? 'Patient';
        $waText      = urlencode("Dear {$patientName}, please find your appointment receipt here: {$receiptUrl}");
    @endphp

    <div class="action-bar no-print">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <span class="brand ms-2">RK Tech Clinic</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-brand" id="btnManageServices"
                    data-bs-toggle="modal" data-bs-target="#manageServicesModal">
                <i class="bi bi-scissors me-1"></i> Manage Services
            </button>
            <a href="{{ route('appointment-products.create') }}?appointment_id={{ $appointment->id }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-bag-plus me-1"></i> Add Products
            </a>
            <a href="{{ route('appointment.receipt', $appointment->id) }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-receipt me-1"></i> Products Receipt
            </a>
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-wa">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
            <a href="{{ route('generate-pdf', $appointment->id) }}" class="btn btn-sm btn-brand">
                <i class="bi bi-printer me-1"></i> Print / PDF
            </a>
        </div>
    </div>

    @if(request('saved'))
    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0 py-2" role="alert" style="font-size:14px;">
        <i class="bi bi-check-circle me-1"></i> Products saved successfully!
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Receipt card ─────────────────────────────────────────────────── --}}
    <div class="receipt-wrap">
        <div class="receipt-card">

            {{-- Header --}}
            <div class="receipt-header d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2><i class="bi bi-receipt me-2"></i>Receipt</h2>
                    <div class="receipt-no">
                        #{{ $appointment->serial_series ?? $appointment->serial_number ?? $appointment->id }}
                        &nbsp;·&nbsp; {{ $appointment->created_at->format('d M Y') }}
                    </div>
                </div>
                <div class="logo-wrap text-end">
                    <img src="{{ asset('public/images/avatar/DMDLOGO.jpg') }}" alt="Logo"
                         onerror="this.style.display='none'">
                </div>
            </div>

            {{-- Info panels --}}
            <div class="info-grid">
                <div class="info-panel">
                    <h6><i class="bi bi-hospital me-1"></i>Clinic Details</h6>
                    <p class="fw-semibold">{{ auth()->user()->business_name }}</p>
                    <p>{{ auth()->user()->address }}</p>
                    @if(auth()->user()->phone)
                    <p><span class="label">Phone: </span>{{ auth()->user()->phone }}</p>
                    @endif
                    @if(auth()->user()->business_email)
                    <p><span class="label">Email: </span>{{ auth()->user()->business_email }}</p>
                    @endif
                </div>
                <div class="info-panel">
                    <h6><i class="bi bi-person me-1"></i>Patient Details</h6>
                    <p class="fw-semibold">{{ $appointment->Customer->name }}</p>
                    @if($appointment->phone)
                    <p><span class="label">Phone: </span>{{ $appointment->phone }}</p>
                    @endif
                    @if($appointment->Customer->email)
                    <p><span class="label">Email: </span>{{ $appointment->Customer->email }}</p>
                    @endif
                </div>
            </div>

            {{-- Services ──────────────────────────────────────────────────── --}}
            <div class="section-heading"><i class="bi bi-scissors me-1"></i>Services</div>
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Discounted</th>
                        <th class="text-right">Final</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointment->appointmentService as $sale)
                    <tr>
                        <td>{{ $sale->name }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($sale->price, 2) }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($sale->discounted_price, 2) }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($sale->discounted_price, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td colspan="3" class="text-right">Subtotal</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($appointment->subtotal_discounted_price, 2) }}</td>
                    </tr>
                    @if(($appointment->discount ?? 0) > 0)
                    <tr class="subtotal">
                        <td colspan="3" class="text-right">Discount</td>
                        <td class="text-right text-danger">— {{ auth()->user()->currency }}{{ number_format($appointment->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Services Total</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($appointment->subtotal_discounted_price_after_discount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Products ──────────────────────────────────────────────────── --}}
            @if($appointment->products && $appointment->products->count())
            @php $productTotal = $appointment->products->sum('total_price'); @endphp

            <div class="section-heading"><i class="bi bi-bag me-1"></i>Products / Dispensed Items</div>
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointment->products as $ap)
                    <tr>
                        <td>
                            {{ $ap->product_name }}
                            @if($ap->variation)
                            <br><small class="text-muted">{{ $ap->variation->name }}</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $ap->quantity }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($ap->unit_price, 2) }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($ap->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Products Total</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($productTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Products total bar --}}
            <table class="receipt-table">
                <tbody>
                    <tr class="grand-row">
                        <td colspan="3" class="text-right">Products Total</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($productTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- Note --}}
            @if(auth()->user()->custom_note)
            <div class="info-panel" style="border-top:1px solid #f0f0f0;">
                <h6><i class="bi bi-info-circle me-1"></i>{{ auth()->user()->custom_note_heading ?: 'Note' }}</h6>
                <p>{{ auth()->user()->custom_note }}</p>
            </div>
            @endif

            <div class="receipt-footer">
                Invoice generated on {{ now()->format('d M Y, H:i') }} &nbsp;·&nbsp; Thank you for visiting us!
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ══════════════════════════════════════════════════════════════════════
         Manage Services Modal
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="manageServicesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                {{-- Header --}}
                <div class="modal-header" style="background:linear-gradient(90deg,#B1083C,#d13729);">
                    <h5 class="modal-title text-white fw-semibold">
                        <i class="bi bi-scissors me-2"></i>Manage Services
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">

                    {{-- Loading indicator --}}
                    <div id="msv-loading" class="text-center py-5">
                        <div class="spinner-border text-danger"></div>
                        <p class="mt-2 text-muted small">Loading services…</p>
                    </div>

                    {{-- Main content (hidden until loaded) --}}
                    <div id="msv-content" class="d-none p-3">

                        {{-- Services table --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-hover table-sm align-middle" style="font-size:.875rem">
                                <thead>
                                    <tr style="background:linear-gradient(90deg,#B1083C,#d13729);color:#fff">
                                        <th class="ps-3">Service</th>
                                        <th style="min-width:130px">Price</th>
                                        <th style="min-width:110px">Discount</th>
                                        <th style="min-width:90px">Final</th>
                                        <th class="text-center" style="min-width:90px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="msv-tbody">
                                    <tr><td colspan="5" class="text-center text-muted py-3">
                                        <div class="spinner-border spinner-border-sm text-danger me-1"></div> Loading…
                                    </td></tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Add new service form --}}
                        <div class="border rounded-3 p-3 mb-3" style="background:#f8f9ff">
                            <p class="fw-semibold small mb-2 text-primary"><i class="bi bi-plus-circle me-1"></i>Add New Service</p>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Service <span class="text-danger">*</span></label>
                                    <select id="msv-add-cat" class="form-select form-select-sm border-secondary">
                                        <option value="">— Select service —</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Price <span class="text-danger">*</span></label>
                                    <input type="number" id="msv-add-price" class="form-control form-control-sm border-secondary" min="0" step="0.01" placeholder="0.00">
                                    <div id="msv-add-benchmark-warn" class="d-none mt-1" style="font-size:.78rem;color:#d97706">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i><span id="msv-add-benchmark-text"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Per-service Discount</label>
                                    <input type="number" id="msv-add-discount" class="form-control form-control-sm border-secondary" min="0" step="0.01" value="0" placeholder="0.00">
                                </div>
                                <div class="col-md-2">
                                    <button id="msv-add-btn" class="btn btn-sm w-100 text-white"
                                            style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;">
                                        <i class="bi bi-plus-circle me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                            <div id="msv-add-error" class="alert alert-danger d-none mt-2 py-1 small mb-0"></div>
                        </div>

                        {{-- Appointment-level discount --}}
                        <div class="border rounded-3 p-3" style="background:#fffef5">
                            <p class="fw-semibold small mb-2" style="color:#92400e"><i class="bi bi-tag me-1"></i>Manual Appointment Discount</p>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Discount Amount</label>
                                    <input type="number" id="msv-discount" class="form-control form-control-sm border-secondary" min="0" step="0.01" value="0" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <button id="msv-discount-btn" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-check-circle me-1"></i>Apply Discount
                                    </button>
                                </div>
                                <div id="msv-discount-ok" class="col-md-5 d-none">
                                    <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Discount saved.</span>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /msv-content --}}
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-brand" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Receipt
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
    (function () {
        const APPT_ID  = {{ $appointment->id }};
        const BASE_URL = '/appointments/' + APPT_ID;
        const CSRF     = () => document.querySelector('meta[name="csrf-token"]').content;
        let allCategories = [];

        // ── Open modal ──────────────────────────────────────────────────────
        document.getElementById('manageServicesModal').addEventListener('show.bs.modal', loadAll);

        function loadAll() {
            document.getElementById('msv-loading').classList.remove('d-none');
            document.getElementById('msv-content').classList.add('d-none');

            fetch(BASE_URL + '/services', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                allCategories = data.categories || [];
                renderServices(data.services || []);
                populateCategoryDropdown(allCategories);
                document.getElementById('msv-discount').value = data.discount ?? 0;
                document.getElementById('msv-loading').classList.add('d-none');
                document.getElementById('msv-content').classList.remove('d-none');
            })
            .catch(() => {
                document.getElementById('msv-loading').innerHTML =
                    '<p class="text-danger py-3 text-center">Failed to load services. Please try again.</p>';
            });
        }

        // ── Render services table ────────────────────────────────────────────
        function renderServices(services) {
            const tbody = document.getElementById('msv-tbody');
            if (!services.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><i class="bi bi-scissors me-1"></i>No services on this appointment.</td></tr>';
                return;
            }
            tbody.innerHTML = services.map(s => {
                const bp = parseFloat(s.benchmark_price) || 0;
                const pr = parseFloat(s.price) || 0;
                const benchWarnHidden = (bp > 0 && pr < bp) ? '' : 'd-none';
                return `
                <tr data-svc-id="${s.id}">
                    <td class="ps-3 fw-semibold">${esc(s.name)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm msv-row-price" value="${pr}"
                               min="0" step="0.01" style="width:100px"
                               data-benchmark="${bp}">
                        <div class="msv-bench-warn ${benchWarnHidden} mt-1" style="font-size:.75rem;color:#d97706">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Below benchmark (${bp})
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm msv-row-discount" value="${parseFloat(s.discount)||0}"
                               min="0" step="0.01" style="width:80px">
                    </td>
                    <td class="fw-semibold" style="color:#B1083C" id="msv-final-${s.id}">
                        ${fmtCurrency(parseFloat(s.discounted_price)||0)}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success py-0 px-1 btn-svc-save" data-id="${s.id}" title="Save">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger py-0 px-1 btn-svc-del ms-1" data-id="${s.id}" title="Delete">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
        }

        // ── Populate category dropdown ────────────────────────────────────────
        function populateCategoryDropdown(cats) {
            const sel = document.getElementById('msv-add-cat');
            sel.innerHTML = '<option value="">— Select service —</option>' +
                cats.map(c => `<option value="${c.id}" data-price="${c.price||0}" data-benchmark="${c.benchmark_price||0}">${esc(c.name)}</option>`).join('');
            document.getElementById('msv-add-price').value = '';
            document.getElementById('msv-add-discount').value = '0';
        }

        // ── Category select → pre-fill price + benchmark check ───────────────
        document.getElementById('msv-add-cat').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const price = parseFloat(opt.dataset.price) || 0;
            const benchmark = parseFloat(opt.dataset.benchmark) || 0;
            document.getElementById('msv-add-price').value = price || '';
            checkBenchmarkAdd(price, benchmark);
        });

        document.getElementById('msv-add-price').addEventListener('input', function () {
            const opt = document.getElementById('msv-add-cat').options[document.getElementById('msv-add-cat').selectedIndex];
            const benchmark = parseFloat(opt?.dataset?.benchmark) || 0;
            checkBenchmarkAdd(parseFloat(this.value) || 0, benchmark);
        });

        function checkBenchmarkAdd(price, benchmark) {
            const warn = document.getElementById('msv-add-benchmark-warn');
            const txt  = document.getElementById('msv-add-benchmark-text');
            if (benchmark > 0 && price < benchmark) {
                txt.textContent = 'Price is below benchmark (' + benchmark + ')';
                warn.classList.remove('d-none');
            } else {
                warn.classList.add('d-none');
            }
        }

        // ── Live price/discount change → benchmark warn + update final ───────
        document.addEventListener('input', function (e) {
            const row = e.target.closest('tr[data-svc-id]');
            if (!row) return;
            const priceInput    = row.querySelector('.msv-row-price');
            const discountInput = row.querySelector('.msv-row-discount');
            const benchWarn     = row.querySelector('.msv-bench-warn');
            const bp = parseFloat(priceInput.dataset.benchmark) || 0;
            const pr = parseFloat(priceInput.value) || 0;
            const dc = parseFloat(discountInput.value) || 0;

            if (benchWarn) {
                if (bp > 0 && pr < bp) benchWarn.classList.remove('d-none');
                else benchWarn.classList.add('d-none');
            }
            const finalEl = document.getElementById('msv-final-' + row.dataset.svcId);
            if (finalEl) finalEl.textContent = fmtCurrency(Math.max(0, pr - dc));
        });

        // ── Save service ─────────────────────────────────────────────────────
        document.addEventListener('click', function (e) {
            const saveBtn = e.target.closest('.btn-svc-save');
            if (saveBtn) {
                const id  = saveBtn.dataset.id;
                const row = document.querySelector(`tr[data-svc-id="${id}"]`);
                if (!row) return;
                const price    = parseFloat(row.querySelector('.msv-row-price').value) || 0;
                const discount = parseFloat(row.querySelector('.msv-row-discount').value) || 0;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(BASE_URL + '/services/' + id, {
                    method : 'PUT',
                    headers: {
                        'Content-Type'    : 'application/json',
                        'X-CSRF-TOKEN'    : CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept'          : 'application/json',
                    },
                    body: JSON.stringify({ price, discount }),
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        saveBtn.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
                        setTimeout(() => { saveBtn.innerHTML = '<i class="bi bi-check-lg"></i>'; saveBtn.disabled = false; }, 1200);
                    }
                })
                .catch(() => { saveBtn.innerHTML = '<i class="bi bi-check-lg"></i>'; saveBtn.disabled = false; });
            }

            // ── Delete service ───────────────────────────────────────────────
            const delBtn = e.target.closest('.btn-svc-del');
            if (delBtn) {
                const id = delBtn.dataset.id;
                if (!confirm('Remove this service from the appointment?')) return;
                delBtn.disabled = true;

                fetch(BASE_URL + '/services/' + id, {
                    method : 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN'    : CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept'          : 'application/json',
                    },
                })
                .then(r => r.json())
                .then(d => { if (d.success) loadAll(); })
                .catch(() => { delBtn.disabled = false; });
            }
        });

        // ── Add service ──────────────────────────────────────────────────────
        document.getElementById('msv-add-btn').addEventListener('click', function () {
            const catId   = document.getElementById('msv-add-cat').value;
            const price   = parseFloat(document.getElementById('msv-add-price').value) || 0;
            const discount = parseFloat(document.getElementById('msv-add-discount').value) || 0;
            const errEl   = document.getElementById('msv-add-error');

            if (!catId) { errEl.textContent = 'Please select a service.'; errEl.classList.remove('d-none'); return; }
            if (!price && price !== 0) { errEl.textContent = 'Please enter a price.'; errEl.classList.remove('d-none'); return; }
            errEl.classList.add('d-none');

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding…';

            fetch(BASE_URL + '/services', {
                method : 'POST',
                headers: {
                    'Content-Type'    : 'application/json',
                    'X-CSRF-TOKEN'    : CSRF(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept'          : 'application/json',
                },
                body: JSON.stringify({ service_id: catId, price, discount }),
            })
            .then(r => {
                if (r.status === 422) return r.json().then(d => { throw { validation: d.errors }; });
                if (!r.ok) throw new Error('Server error');
                return r.json();
            })
            .then(d => {
                if (d.success) {
                    document.getElementById('msv-add-cat').value = '';
                    document.getElementById('msv-add-price').value = '';
                    document.getElementById('msv-add-discount').value = '0';
                    document.getElementById('msv-add-benchmark-warn').classList.add('d-none');
                    loadAll();
                }
            })
            .catch(err => {
                if (err.validation) {
                    errEl.innerHTML = Object.values(err.validation).flat().map(m => `<div>• ${m}</div>`).join('');
                } else {
                    errEl.textContent = err.message || 'An error occurred.';
                }
                errEl.classList.remove('d-none');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add';
            });
        });

        // ── Apply appointment discount ────────────────────────────────────────
        document.getElementById('msv-discount-btn').addEventListener('click', function () {
            const discount = parseFloat(document.getElementById('msv-discount').value) || 0;
            const okEl = document.getElementById('msv-discount-ok');
            this.disabled = true;

            fetch(BASE_URL + '/discount', {
                method : 'PATCH',
                headers: {
                    'Content-Type'    : 'application/json',
                    'X-CSRF-TOKEN'    : CSRF(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept'          : 'application/json',
                },
                body: JSON.stringify({ discount }),
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    okEl.classList.remove('d-none');
                    setTimeout(() => okEl.classList.add('d-none'), 2500);
                }
            })
            .catch(() => {})
            .finally(() => { this.disabled = false; });
        });

        // ── Helpers ───────────────────────────────────────────────────────────
        function esc(str) {
            if (!str) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        function fmtCurrency(n) {
            return '{{ auth()->user()->currency }}' + Number(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    })();
    </script>
</body>
</html>
