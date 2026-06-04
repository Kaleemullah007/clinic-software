<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt — {{ $appointment->serial_series ?? $appointment->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
            padding: 20px 28px;
        }

        /* ── Header ─────────────────────────────────────── */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #B1083C;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header-left { width: 40%; vertical-align: middle; }
        .header-right { width: 60%; vertical-align: middle; text-align: right; }

        .clinic-logo { max-height: 80px; max-width: 200px; }
        .clinic-name {
            font-size: 14px;
            font-weight: bold;
            color: #B1083C;
            margin-bottom: 3px;
        }
        .clinic-info { font-size: 11px; color: #444; line-height: 1.6; }
        .clinic-info strong { color: #1a1a1a; }

        /* ── Invoice title bar ──────────────────────────── */
        .invoice-title-bar {
            background: #B1083C;
            color: #fff;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 6px 0;
            margin-bottom: 14px;
        }

        /* ── Patient + Invoice info block ───────────────── */
        .info-table {
            width: 100%;
            margin-bottom: 16px;
            border: 1px solid #e0e0e0;
        }
        .info-table td {
            padding: 5px 10px;
            font-size: 11.5px;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-table .label {
            color: #666;
            font-weight: bold;
            width: 130px;
            white-space: nowrap;
        }
        .info-table .value { color: #1a1a1a; }
        .info-left { width: 50%; border-right: 1px solid #e0e0e0; }
        .info-right { width: 50%; }
        .invoice-id {
            font-size: 18px;
            font-weight: bold;
            color: #B1083C;
            text-align: right;
        }
        .invoice-date { font-size: 11px; color: #555; text-align: right; }

        /* ── Services table ─────────────────────────────── */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .services-table thead tr {
            background: #B1083C;
            color: #fff;
        }
        .services-table thead th {
            padding: 7px 10px;
            font-size: 11.5px;
            font-weight: bold;
            text-align: left;
            border: none;
        }
        .services-table thead th.text-right { text-align: right; }
        .services-table tbody tr:nth-child(even) { background: #fdf4f6; }
        .services-table tbody td {
            padding: 6px 10px;
            font-size: 11.5px;
            border-bottom: 1px solid #f0e0e4;
            color: #1a1a1a;
        }
        .services-table tbody td.text-right { text-align: right; }
        .services-table tbody td.text-center { text-align: center; }

        /* ── Totals block ───────────────────────────────── */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .totals-table td {
            padding: 5px 10px;
            font-size: 11.5px;
            border-bottom: 1px solid #f0e0e4;
        }
        .totals-table .totals-label {
            text-align: right;
            color: #555;
            width: 75%;
            padding-right: 20px;
        }
        .totals-table .totals-value {
            text-align: right;
            font-weight: bold;
            color: #1a1a1a;
            white-space: nowrap;
            background: #fdf4f6;
            min-width: 100px;
        }
        .totals-table .grand-row td {
            background: #B1083C;
            color: #fff;
            font-weight: bold;
            font-size: 12.5px;
        }
        .totals-table .balance-row td {
            color: #B1083C;
            font-weight: bold;
            font-size: 12px;
        }
        .totals-table .paid-row td { color: #198754; font-weight: bold; }

        /* ── Footer ─────────────────────────────────────── */
        .footer-note {
            margin-top: 18px;
            padding: 10px 12px;
            background: #fdf4f6;
            border-left: 3px solid #B1083C;
            font-size: 11px;
            color: #555;
        }
        .footer-line {
            margin-top: 14px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    @php
        $currency   = $receiptMessage = '';
        $settings   = \App\Models\Setting::whereIn('key_name', ['currency_symbol','receipt_message'])->pluck('key_value','key_name');
        $currency   = $settings['currency_symbol'] ?? 'PKR';
        $receiptMsg = $settings['receipt_message'] ?? 'Thank you for visiting us!';

        $doctor     = $appointment->doctor;
        $patient    = $appointment->patient ?? $appointment->customer;
        $services   = $appointment->appointmentService;

        $grossTotal    = $services->sum('price');
        $discountFlat  = $appointment->subtotal_price_after_discount - $appointment->subtotal_discounted_price_after_discount;
        $netTotal      = $appointment->subtotal_discounted_price_after_discount ?? 0;
        $paidAmount    = $appointment->paid_amount ?? 0;
        $balance       = $appointment->remaining_amount ?? ($netTotal - $paidAmount);
    @endphp

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-left">
                <img src="{{ public_path('images/dmd-logo.png') }}" class="clinic-logo" alt="DMD Logo">
            </td>
            <td class="header-right">
                <div class="clinic-name">D.M.D Aesthetic, Dental &amp; Hair Transplant Clinic</div>
                <div class="clinic-info">
                    <strong>Address:</strong> Super Market, F-6 Markaz Islamabad<br>
                    <strong>Phone:</strong> 03335560507<br>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Invoice title ────────────────────────────────────────────────── --}}
    <div class="invoice-title-bar">SALE INVOICE</div>

    {{-- ── Patient + Invoice info ───────────────────────────────────────── --}}
    <table class="info-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-left">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td class="value" style="font-weight:bold;color:#B1083C;">{{ $appointment->serial_series ?? $appointment->id }}</td>
                    </tr>
                    <tr>
                        <td class="label">Patient Name</td>
                        <td class="value">{{ $patient->name ?? $appointment->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mobile</td>
                        <td class="value">{{ $appointment->phone ?? $patient->phone ?? '—' }}</td>
                    </tr>
                    @if($patient && $patient->email)
                    <tr>
                        <td class="label">Email</td>
                        <td class="value">{{ $patient->email }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Patient Type</td>
                        <td class="value">{{ ucfirst($appointment->is_paid === 'paid' ? 'Cash' : 'Credit') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Method</td>
                        <td class="value">{{ $appointment->is_paid === 'paid' ? 'Paid' : 'Unpaid' }}</td>
                    </tr>
                </table>
            </td>
            <td class="info-right" style="padding: 10px 14px; vertical-align: top;">
                <div class="invoice-id">{{ $appointment->serial_series ?? ('#' . $appointment->id) }}</div>
                <div class="invoice-date">
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d-m-Y') }}
                </div>
                @if($doctor)
                <div class="invoice-date" style="margin-top:6px;">
                    <strong>Doctor Reference:</strong><br>
                    <span style="font-size:12px;font-weight:bold;color:#1a1a1a;">{{ $doctor->name }}</span>
                </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ── Services table ───────────────────────────────────────────────── --}}
    <table class="services-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:40px;">Sr. No.</th>
                <th>Service Name</th>
                <th class="text-right" style="width:110px;">Original Price</th>
                <th class="text-right" style="width:110px;">Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $i => $s)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $s->name }}</td>
                <td class="text-right">{{ $currency }} {{ number_format($s->price, 2) }}</td>
                <td class="text-right">{{ $currency }} {{ number_format($s->discounted_price ?? $s->price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#999;padding:14px;">No services recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Totals ────────────────────────────────────────────────────────── --}}
    <table class="totals-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="totals-label">Gross Total</td>
            <td class="totals-value">{{ $currency }} {{ number_format($grossTotal, 2) }}</td>
        </tr>
        @if($discountFlat > 0)
        <tr>
            <td class="totals-label">Discount (Flat)</td>
            <td class="totals-value" style="color:#dc3545;">- {{ $currency }} {{ number_format($discountFlat, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-row">
            <td class="totals-label" style="color:#fff;font-weight:bold;">Net Total</td>
            <td class="totals-value" style="color:#fff;background:#B1083C;">{{ $currency }} {{ number_format($netTotal, 2) }}</td>
        </tr>
        <tr class="paid-row">
            <td class="totals-label" style="color:#198754;">Paid Amount</td>
            <td class="totals-value" style="color:#198754;">{{ $currency }} {{ number_format($paidAmount, 2) }}</td>
        </tr>
        <tr class="balance-row">
            <td class="totals-label">Balance</td>
            <td class="totals-value" style="color:{{ $balance > 0 ? '#dc3545' : '#198754' }};">
                {{ $currency }} {{ number_format($balance, 2) }}
            </td>
        </tr>
    </table>

    {{-- ── Footer note ──────────────────────────────────────────────────── --}}
    @if($receiptMsg)
    <div class="footer-note">{{ $receiptMsg }}</div>
    @endif

    <div class="footer-line">
        Invoice generated on {{ now()->format('d M Y, H:i') }} &nbsp;·&nbsp; RK Tech Clinic System
    </div>

</body>
</html>
