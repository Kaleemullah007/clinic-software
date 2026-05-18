<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $appointment->serial_number ?? $appointment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
        $rawPhone = preg_replace('/[^0-9]/', '', $appointment->phone ?? '');
        if (str_starts_with($rawPhone, '0'))       { $waPhone = '92' . substr($rawPhone, 1); }
        elseif (str_starts_with($rawPhone, '92'))  { $waPhone = $rawPhone; }
        else                                        { $waPhone = '92' . $rawPhone; }
        $receiptUrl  = route('generate-pdf', $appointment->id);
        $patientName = $appointment->Customer->name ?? 'Patient';
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

            {{-- Grand total --}}
            @php $grandTotal = ($appointment->subtotal_discounted_price_after_discount ?? 0) + $productTotal; @endphp
            <table class="receipt-table">
                <tbody>
                    <tr class="grand-row">
                        <td colspan="3" class="text-right">Grand Total (Services + Products)</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($grandTotal, 2) }}</td>
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
</body>
</html>
