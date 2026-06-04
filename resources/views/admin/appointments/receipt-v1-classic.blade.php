<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt {{ $appointment->serial_series ?? $appointment->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; font-size:11px; color:#111; background:#fff; padding:18px 24px; }

/* ── Header ── */
.hdr { width:100%; border-bottom:3px double #333; padding-bottom:10px; margin-bottom:10px; }
.hdr-left  { width:38%; vertical-align:middle; }
.hdr-right { width:62%; vertical-align:middle; text-align:right; }
.clinic-logo { max-height:72px; max-width:180px; }
.clinic-name { font-size:13px; font-weight:bold; color:#111; }
.clinic-sub  { font-size:10px; color:#444; line-height:1.7; margin-top:3px; }

/* ── Invoice number block (big, top-right like Citi Lab) ── */
.inv-block { text-align:right; margin-bottom:6px; }
.inv-serial { font-size:26px; font-weight:bold; color:#111; letter-spacing:1px; }
.inv-label  { font-size:10px; color:#666; }
.inv-date   { font-size:10.5px; color:#333; margin-top:4px; }

/* ── Info grid (two panels side by side) ── */
.info-outer { width:100%; border:1px solid #ccc; margin-bottom:12px; border-collapse:collapse; }
.info-outer td { padding:0; vertical-align:top; }
.info-panel { width:50%; padding:8px 12px; }
.info-panel-right { border-left:1px solid #ccc; }
.info-panel table { width:100%; }
.info-panel table td { padding:2px 0; font-size:10.5px; }
.info-panel .lbl { color:#555; width:110px; font-weight:bold; }
.info-panel .val { color:#111; }
.section-head { font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#555; border-bottom:1px solid #ddd; padding-bottom:3px; margin-bottom:5px; }

/* ── Services table ── */
.svc-table { width:100%; border-collapse:collapse; margin-bottom:0; }
.svc-table thead tr { background:#2c2c2c; color:#fff; }
.svc-table thead th { padding:6px 10px; font-size:10.5px; font-weight:bold; border:1px solid #444; text-align:center; }
.svc-table thead th.left { text-align:left; }
.svc-table tbody tr:nth-child(even) { background:#f7f7f7; }
.svc-table tbody td { padding:5px 10px; font-size:10.5px; border:1px solid #ddd; text-align:center; }
.svc-table tbody td.left { text-align:left; }

/* ── Totals ── */
.totals-table { width:100%; border-collapse:collapse; }
.totals-table td { padding:4px 10px; font-size:10.5px; border:1px solid #ddd; }
.totals-table .lbl { text-align:right; color:#555; padding-right:16px; }
.totals-table .val { text-align:right; font-weight:bold; min-width:90px; }
.totals-table .net-row td { background:#2c2c2c; color:#fff; font-size:11.5px; font-weight:bold; }
.totals-table .paid-row td { color:#197a3e; background:#f0fff4; }
.totals-table .bal-row  td { color:#b91c1c; background:#fff5f5; font-weight:bold; }

/* ── Footer ── */
.footer-note { margin-top:14px; font-size:10px; color:#555; border-top:1px dashed #bbb; padding-top:8px; text-align:center; }
</style>
</head>
<body>

@php
    $s        = \App\Models\Setting::whereIn('key_name',['currency_symbol','receipt_message'])->pluck('key_value','key_name');
    $currency = $s['currency_symbol'] ?? 'PKR';
    $footMsg  = $s['receipt_message'] ?? 'Thank you for visiting us!';
    $doctor   = $appointment->doctor;
    $patient  = $appointment->patient ?? $appointment->customer;
    $services = $appointment->appointmentService;
    $gross    = $services->sum('price');
    $net      = $appointment->subtotal_discounted_price_after_discount ?? 0;
    $disc     = max(0, $gross - $net);
    $paid     = $appointment->paid_amount ?? 0;
    $bal      = $appointment->remaining_amount ?? max(0, $net - $paid);
@endphp

{{-- Header --}}
<table class="hdr" cellpadding="0" cellspacing="0">
    <tr>
        <td class="hdr-left">
            <img src="{{ public_path('images/dmd-logo.png') }}" class="clinic-logo" alt="DMD">
        </td>
        <td class="hdr-right">
            <div class="inv-block">
                <div class="inv-label">INVOICE ID</div>
                <div class="inv-serial">{{ $appointment->serial_series ?? $appointment->id }}</div>
                <div class="inv-date"><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d-m-Y') }}</div>
                @if($doctor)<div class="inv-date" style="margin-top:4px;"><strong>Doctor:</strong> {{ $doctor->name }}</div>@endif
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top:6px;">
            <div class="clinic-name">D.M.D Aesthetic, Dental &amp; Hair Transplant Clinic</div>
            <div class="clinic-sub">Super Market, F-6 Markaz Islamabad &nbsp;|&nbsp; ☎ 03335560507</div>
        </td>
    </tr>
</table>

{{-- Sale Invoice title --}}
<div style="text-align:center;font-size:12px;font-weight:bold;letter-spacing:2px;border:1px solid #ccc;padding:4px 0;margin-bottom:10px;">SALE INVOICE</div>

{{-- Patient info --}}
<table class="info-outer" cellpadding="0" cellspacing="0">
    <tr>
        <td class="info-panel">
            <div class="section-head">Patient Details</div>
            <table cellpadding="0" cellspacing="0">
                <tr><td class="lbl">Name</td><td class="val">{{ $patient->name ?? $appointment->name }}</td></tr>
                <tr><td class="lbl">Mobile</td><td class="val">{{ $appointment->phone ?? $patient->phone ?? '—' }}</td></tr>
                @if(!empty($patient->email))<tr><td class="lbl">Email</td><td class="val">{{ $patient->email }}</td></tr>@endif
                <tr><td class="lbl">Patient Type</td><td class="val">{{ $appointment->is_paid === 'paid' ? 'Cash' : 'Credit' }}</td></tr>
                <tr><td class="lbl">Payment</td><td class="val">{{ $appointment->is_paid === 'paid' ? 'Paid' : 'Unpaid' }}</td></tr>
            </table>
        </td>
        <td class="info-panel info-panel-right">
            <div class="section-head">Appointment Details</div>
            <table cellpadding="0" cellspacing="0">
                <tr><td class="lbl">Invoice No.</td><td class="val" style="font-weight:bold;">{{ $appointment->serial_series ?? $appointment->id }}</td></tr>
                <tr><td class="lbl">Date</td><td class="val">{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td></tr>
                @if($doctor)<tr><td class="lbl">Doctor Ref.</td><td class="val" style="font-weight:bold;">{{ $doctor->name }}</td></tr>@endif
                <tr><td class="lbl">Services</td><td class="val">{{ $services->count() }}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- Services table --}}
<table class="svc-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th style="width:36px;">Sr.</th>
            <th class="left">Service Name</th>
            <th style="width:110px;">Original Price</th>
            <th style="width:110px;">Final Price</th>
        </tr>
    </thead>
    <tbody>
        @forelse($services as $i => $sv)
        <tr>
            <td>{{ $i+1 }}</td>
            <td class="left">{{ $sv->name }}</td>
            <td>{{ $currency }} {{ number_format($sv->price,2) }}</td>
            <td>{{ $currency }} {{ number_format($sv->discounted_price ?? $sv->price,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#999;padding:12px;">No services recorded.</td></tr>
        @endforelse

        {{-- spacer row before totals --}}
        <tr><td colspan="4" style="padding:0;"></td></tr>
    </tbody>
</table>

<table class="totals-table" cellpadding="0" cellspacing="0">
    <tr><td class="lbl" colspan="2">Gross Total</td><td class="val">{{ $currency }} {{ number_format($gross,2) }}</td></tr>
    @if($disc > 0)
    <tr><td class="lbl" colspan="2">Discount (Flat)</td><td class="val" style="color:#b91c1c;">{{ $currency }} {{ number_format($disc,2) }}</td></tr>
    @endif
    <tr class="net-row">
        <td class="lbl" colspan="2" style="color:#fff;">Net Total</td>
        <td class="val" style="color:#fff;">{{ $currency }} {{ number_format($net,2) }}</td>
    </tr>
    <tr class="paid-row">
        <td class="lbl" colspan="2">Paid Amount</td>
        <td class="val">{{ $currency }} {{ number_format($paid,2) }}</td>
    </tr>
    <tr class="bal-row">
        <td class="lbl" colspan="2">Balance</td>
        <td class="val">{{ $currency }} {{ number_format($bal,2) }}</td>
    </tr>
</table>

<div class="footer-note">{{ $footMsg }}</div>
<div style="text-align:center;font-size:9px;color:#aaa;margin-top:6px;">Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
