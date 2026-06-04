<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt {{ $appointment->serial_series ?? $appointment->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; font-size:11px; color:#222; background:#fff; }

/* ── Full-width dark header ── */
.main-header { background:#1a1a2e; padding:0; }
.header-top  { padding:16px 24px 12px; }
.header-top table { width:100%; }
.logo-td  { width:45%; vertical-align:middle; }
.logo-td img { max-height:66px; max-width:170px; }
.inv-td   { width:55%; vertical-align:middle; text-align:right; }
.inv-num  { font-size:30px; font-weight:bold; color:#fff; letter-spacing:3px; line-height:1; }
.inv-type { font-size:9px; color:#aab; letter-spacing:2px; text-transform:uppercase; }

/* ── Red accent strip ── */
.accent-strip { background:#B1083C; padding:7px 24px; }
.accent-strip table { width:100%; }
.clinic-name { font-size:12px; font-weight:bold; color:#fff; }
.clinic-addr { font-size:10px; color:#ffd6e0; text-align:right; }

/* ── Body ── */
.body { padding:16px 24px; }

/* ── Status badge row ── */
.status-row { margin-bottom:14px; }
.badge-paid   { background:#d1fae5; color:#065f46; padding:3px 10px; font-size:10px; font-weight:bold; border-radius:2px; }
.badge-unpaid { background:#fee2e2; color:#991b1b; padding:3px 10px; font-size:10px; font-weight:bold; border-radius:2px; }
.inv-date-tag { font-size:10px; color:#888; }

/* ── Info grid ── */
.info-grid { width:100%; border-collapse:collapse; margin-bottom:14px; }
.info-grid td { vertical-align:top; width:50%; padding:0; }
.info-box { border:1px solid #e0e0e0; padding:8px 12px; height:100%; }
.info-box-right { border-left:none; }
.info-box .head { font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:1px;
                  color:#fff; background:#B1083C; padding:2px 6px; margin:-8px -12px 8px; }
.info-box table td { font-size:10.5px; padding:2px 0; }
.info-box .lbl { color:#999; width:105px; }
.info-box .val { color:#111; font-weight:500; }

/* ── Services ── */
.svc-table { width:100%; border-collapse:collapse; }
.svc-table thead tr { background:#1a1a2e; }
.svc-table thead th { color:#fff; padding:7px 10px; font-size:10px; text-transform:uppercase;
                      letter-spacing:.5px; text-align:center; }
.svc-table thead th.left { text-align:left; }
.svc-table tbody tr:nth-child(odd)  { background:#fafafa; }
.svc-table tbody tr:nth-child(even) { background:#fff; }
.svc-table tbody tr:hover { background:#fdf5f7; }
.svc-table tbody td { padding:6px 10px; font-size:10.5px; border-bottom:1px solid #efefef; text-align:center; }
.svc-table tbody td.left { text-align:left; }

/* ── Totals ── */
.tot-wrap { background:#f9f9f9; border:1px solid #e8e8e8; padding:0; margin-top:0; }
.tot-table { width:100%; border-collapse:collapse; }
.tot-table td { padding:5px 12px; font-size:10.5px; border-bottom:1px solid #efefef; }
.tot-table .lbl { color:#666; text-align:right; padding-right:20px; }
.tot-table .val { text-align:right; font-weight:bold; color:#222; min-width:100px; }
.tot-net td { background:#1a1a2e; color:#fff; font-size:12px; font-weight:bold; }
.tot-paid td { color:#197a3e; background:#f0fff4; }
.tot-bal td  { color:#b91c1c; background:#fff5f5; font-weight:bold; }

/* ── Footer ── */
.footer { background:#1a1a2e; color:#aab; text-align:center; font-size:10px; padding:9px 24px; margin-top:14px; }
.footer .msg { color:#ffd6e0; font-size:11px; font-weight:bold; margin-bottom:3px; }
</style>
</head>
<body>

@php
    $s        = \App\Models\Setting::whereIn('key_name',['currency_symbol','receipt_message'])->pluck('key_value','key_name');
    $currency = $s['currency_symbol'] ?? 'PKR';
    $footMsg  = $s['receipt_message'] ?? 'Thank you for visiting D.M.D Clinic!';
    $doctor   = $appointment->doctor;
    $patient  = $appointment->patient ?? $appointment->customer;
    $services = $appointment->appointmentService;
    $gross    = $services->sum('price');
    $net      = $appointment->subtotal_discounted_price_after_discount ?? 0;
    $disc     = max(0, $gross - $net);
    $paid     = $appointment->paid_amount ?? 0;
    $bal      = $appointment->remaining_amount ?? max(0, $net - $paid);
@endphp

{{-- Dark header --}}
<div class="main-header">
    <div class="header-top">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo-td"><img src="{{ public_path('images/dmd-logo.png') }}" alt="DMD"></td>
                <td class="inv-td">
                    <div class="inv-type">Sale Invoice</div>
                    <div class="inv-num">{{ $appointment->serial_series ?? ('#' . $appointment->id) }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="accent-strip">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="clinic-name">D.M.D Aesthetic, Dental &amp; Hair Transplant Clinic</td>
                <td class="clinic-addr">Super Market, F-6 Markaz Islamabad &nbsp;|&nbsp; 03335560507</td>
            </tr>
        </table>
    </div>
</div>

<div class="body">

    {{-- Status + date row --}}
    <div class="status-row">
        @if($appointment->is_paid === 'paid')
            <span class="badge-paid">✔ PAID</span>
        @else
            <span class="badge-unpaid">✗ UNPAID</span>
        @endif
        <span class="inv-date-tag" style="margin-left:10px;">
            Date: {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}
            @if($doctor) &nbsp;|&nbsp; Doctor: <strong>{{ $doctor->name }}</strong>@endif
        </span>
    </div>

    {{-- Info grid --}}
    <table class="info-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding-right:0;">
                <div class="info-box">
                    <div class="head">Patient Details</div>
                    <table cellpadding="0" cellspacing="0">
                        <tr><td class="lbl">Name</td><td class="val">{{ $patient->name ?? $appointment->name }}</td></tr>
                        <tr><td class="lbl">Mobile</td><td class="val">{{ $appointment->phone ?? $patient->phone ?? '—' }}</td></tr>
                        @if(!empty($patient->email))<tr><td class="lbl">Email</td><td class="val">{{ $patient->email }}</td></tr>@endif
                        <tr><td class="lbl">Patient Type</td><td class="val">{{ $appointment->is_paid === 'paid' ? 'Cash' : 'Credit' }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="info-box info-box-right">
                    <div class="head">Invoice Details</div>
                    <table cellpadding="0" cellspacing="0">
                        <tr><td class="lbl">Invoice No.</td><td class="val" style="color:#B1083C;">{{ $appointment->serial_series ?? $appointment->id }}</td></tr>
                        <tr><td class="lbl">Date</td><td class="val">{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td></tr>
                        @if($doctor)<tr><td class="lbl">Doctor</td><td class="val">{{ $doctor->name }}</td></tr>@endif
                        <tr><td class="lbl">Services</td><td class="val">{{ $services->count() }} item(s)</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Services --}}
    <table class="svc-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:36px;">SR.</th>
                <th class="left">SERVICE NAME</th>
                <th style="width:110px;">LIST PRICE</th>
                <th style="width:110px;">CHARGED</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $i => $sv)
            <tr>
                <td>{{ $i+1 }}</td>
                <td class="left">{{ $sv->name }}</td>
                <td style="color:#999;">{{ $currency }} {{ number_format($sv->price,2) }}</td>
                <td style="font-weight:bold;">{{ $currency }} {{ number_format($sv->discounted_price ?? $sv->price,2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#ccc;padding:14px;">No services recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="tot-wrap">
        <table class="tot-table" cellpadding="0" cellspacing="0">
            <tr><td class="lbl" colspan="2">Gross Total</td><td class="val">{{ $currency }} {{ number_format($gross,2) }}</td></tr>
            @if($disc > 0)
            <tr><td class="lbl" colspan="2">Discount</td><td class="val" style="color:#b91c1c;">– {{ $currency }} {{ number_format($disc,2) }}</td></tr>
            @endif
            <tr class="tot-net">
                <td class="lbl" colspan="2" style="color:#fff;">Net Total</td>
                <td class="val" style="color:#fff;">{{ $currency }} {{ number_format($net,2) }}</td>
            </tr>
            <tr class="tot-paid"><td class="lbl" colspan="2">Paid Amount</td><td class="val">{{ $currency }} {{ number_format($paid,2) }}</td></tr>
            <tr class="tot-bal"><td class="lbl" colspan="2">Balance</td><td class="val">{{ $currency }} {{ number_format($bal,2) }}</td></tr>
        </table>
    </div>

</div>

<div class="footer">
    <div class="msg">{{ $footMsg }}</div>
    Invoice generated on {{ now()->format('d M Y, H:i') }}
</div>
</body>
</html>
