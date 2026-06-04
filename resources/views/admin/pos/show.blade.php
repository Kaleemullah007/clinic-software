<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Receipt — {{ $pos->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --brand: #B1083C; --brand2: #d13729; }
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

        .action-bar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .action-bar .brand { font-weight: 700; font-size: 1.1rem; color: var(--brand); }
        .btn-brand { background: linear-gradient(90deg,var(--brand),var(--brand2)); color:#fff; border:none; }
        .btn-brand:hover { background: linear-gradient(90deg,var(--brand2),var(--brand)); color:#fff; }

        .receipt-wrap { max-width: 860px; margin: 32px auto; padding: 0 16px 60px; }
        .receipt-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.09); overflow: hidden; }

        .receipt-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand2) 100%);
            padding: 28px 36px; color: #fff;
        }
        .receipt-header h2 { font-size: 1.6rem; font-weight: 700; margin: 0; }
        .receipt-header .receipt-no { opacity: .85; font-size: .9rem; margin-top: 2px; }
        .receipt-header .logo-wrap img { max-height: 56px; border-radius: 6px; background:#fff; padding:4px 8px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .info-panel { padding: 20px 28px; border-bottom: 1px solid #f0f0f0; }
        .info-panel:nth-child(odd) { border-right: 1px solid #f0f0f0; }
        .info-panel h6 { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--brand); margin-bottom: 10px; }
        .info-panel p { margin: 3px 0; font-size: .875rem; color: #374151; }
        .info-panel .label { color: #9ca3af; font-size: .78rem; }

        .section-heading {
            padding: 14px 28px 8px; font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em; color: #6b7280;
            background: #fafafa; border-top: 1px solid #f0f0f0;
        }
        .receipt-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .receipt-table thead th { padding: 10px 28px; background: #f8f8fb; color: #374151; font-weight: 600; border-bottom: 2px solid #ececec; }
        .receipt-table tbody td { padding: 10px 28px; border-bottom: 1px solid #f4f4f4; color: #374151; }
        .receipt-table tbody tr:last-child td { border-bottom: none; }
        .receipt-table .subtotal td { background: #fafafa; font-weight: 500; color: #6b7280; }
        .receipt-table .total-row td { background: #fff7f9; font-weight: 700; color: var(--brand); border-top: 2px solid #ffe0e8; }
        .receipt-table .grand-row td {
            background: linear-gradient(90deg, var(--brand), var(--brand2));
            color: #fff; font-weight: 700; font-size: .95rem; padding: 14px 28px;
        }
        .text-right { text-align: right; }

        .receipt-footer { padding: 20px 28px; background: #fafafa; border-top: 1px solid #f0f0f0; text-align: center; color: #9ca3af; font-size: .8rem; }

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

    {{-- Action bar --}}
    <div class="action-bar no-print">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <span class="brand ms-2">{{ $pos->clinic?->name ?? auth()->user()->business_name ?? 'RK Tech Clinic' }}</span>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="{{ $pos->payment_status === 'paid' ? 'badge bg-success' : 'badge bg-warning text-dark' }} fs-6 me-1">
                {{ ucfirst($pos->payment_status) }}
            </span>
            <a href="{{ route('pos.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i> New Sale
            </a>
            <button class="btn btn-sm btn-brand" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Receipt card --}}
    <div class="receipt-wrap">
        <div class="receipt-card">

            {{-- Header --}}
            <div class="receipt-header d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2><i class="bi bi-cash-register me-2"></i>POS Receipt</h2>
                    <div class="receipt-no">
                        {{ $pos->order_number }} &nbsp;·&nbsp; {{ $pos->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
                <div class="logo-wrap text-end">
                    <img src="{{ asset('public/images/avatar/DMDLOGO.jpg') }}" alt="Logo" onerror="this.style.display='none'">
                </div>
            </div>

            {{-- Info panels --}}
            <div class="info-grid">
                <div class="info-panel">
                    <h6><i class="bi bi-hospital me-1"></i>Clinic / Business</h6>
                    <p class="fw-semibold">{{ $pos->clinic?->name ?? auth()->user()->business_name ?? '—' }}</p>
                    @if($pos->clinic)
                    <p>{{ $pos->clinic->address ?? '' }}</p>
                    @endif
                    <p><span class="label">Cashier: </span>{{ $pos->creator?->name ?? '—' }}</p>
                </div>
                <div class="info-panel">
                    <h6><i class="bi bi-person me-1"></i>Patient Details</h6>
                    <p class="fw-semibold">{{ $pos->patient?->name ?? '—' }}</p>
                    @if($pos->patient?->phone)
                    <p><span class="label">Phone: </span>{{ $pos->patient->phone }}</p>
                    @endif
                    @if($pos->patient?->email)
                    <p><span class="label">Email: </span>{{ $pos->patient->email }}</p>
                    @endif
                    @if($pos->shipping_address)
                    <p><span class="label">Ship to: </span>{{ $pos->shipping_address }}</p>
                    @endif
                </div>
            </div>

            {{-- Products --}}
            <div class="section-heading"><i class="bi bi-bag me-1"></i>Items</div>
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pos->items as $i => $item)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            @if($item->variation_name)
                            <small class="text-muted">{{ $item->variation_name }}</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach

                    {{-- Subtotal --}}
                    <tr class="subtotal">
                        <td colspan="4" class="text-right">Subtotal</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($pos->subtotal, 2) }}</td>
                    </tr>

                    {{-- Discount --}}
                    @if($pos->discount > 0)
                    <tr class="subtotal">
                        <td colspan="4" class="text-right">Discount</td>
                        <td class="text-right text-danger">— {{ auth()->user()->currency }}{{ number_format($pos->discount, 2) }}</td>
                    </tr>
                    @endif

                    {{-- Tax --}}
                    @if($pos->tax_amount > 0)
                    <tr class="subtotal">
                        <td colspan="4" class="text-right">
                            {{ $pos->tax_label ?: 'Tax' }}
                            @if($pos->tax_rate > 0)<small class="text-muted">({{ $pos->tax_rate }}%)</small>@endif
                        </td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($pos->tax_amount, 2) }}</td>
                    </tr>
                    @endif

                    {{-- Grand total --}}
                    <tr class="grand-row">
                        <td colspan="4" class="text-right">Grand Total</td>
                        <td class="text-right">{{ auth()->user()->currency }}{{ number_format($pos->grand_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Notes --}}
            @if($pos->notes)
            <div class="info-panel" style="border-top:1px solid #f0f0f0;">
                <h6><i class="bi bi-info-circle me-1"></i>Notes</h6>
                <p>{{ $pos->notes }}</p>
            </div>
            @endif

            <div class="receipt-footer">
                POS Receipt &nbsp;·&nbsp; {{ $pos->order_number }} &nbsp;·&nbsp; {{ $pos->created_at->format('d M Y, h:i A') }} &nbsp;·&nbsp; Thank you!
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
