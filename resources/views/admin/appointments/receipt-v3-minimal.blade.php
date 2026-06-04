<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt {{ $appointment->serial_series ?? $appointment->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size:11px; color:#333; background:#fff; padding:30px 32px; }

/* ── Logo + title centred ── */
.header { text-align:center; padding-bottom:18px; border-bottom:1px solid #e0e0e0; margin-bottom:18px; }
.header img { max-height:68px; max-width:200px; display:block; margin:0 auto 8px; }
.header .clinic-name { font-size:13px; font-weight:bold; color:#222; letter-spacing:.5px; }
.header .clinic-sub  { font-size:10px; color:#888; margin-top:3px; }
.header .inv-tag {
    display:inline-block; background:#f5f5f5; border:1px solid #ddd;
    color:#555; font-size:10px; padding:2px 10px; border-radius:20px; margin-top:6px;
}

/* ── Two-col info ── */
.info-row { width:100%; margin-bottom:18px; }
.info-col { width:50%; vertical-align:top; }
.info-col .head { font-size:9px; text-transform:uppercase; letter-spacing:1px; color:#B1083C; font-weight:bold; border-bottom:1px solid #f0d0d6; padding-bottom:4px; margin-bottom:6px; }
.info-col table td { font-size:10.5px; padding:2px 0; vertical-align:top; }
.info-col table .lbl { color:#999; width:100px; }
.info-col table .val { color:#222; }

/* ── Services ── */
.svc-table { width:100%; border-collapse:collapse; margin-bottom:0; }
.svc-table thead th { font-size:9px; text-transform:uppercase; letter-spacing:.8px; color:#B1083C;
                       padding:5px 8px; border-bottom:2px solid #B1083C; text-align:left; }
.svc-table thead th.right { text-align:right; }
.svc-table tbody tr { border-bottom:1px solid #f0f0f0; }
.svc-table tbody td { padding:6px 8px; font-size:10.5px; color:#333; }
.svc-table tbody td.right { text-align:right; }
.svc-table tbody td.center { text-align:center; color:#999; }

/* ── Divider ── */
.divider { border:none; border-top:1px solid #e8e8e8; margin:0; }

/* ── Totals ── */
.tot-table { width:60%; margin-left:40%; border-collapse:collapse; margin-top:0; }
.tot-table td { padding:4px 8px; font-size:10.5px; border-bottom:1px solid #f5f5f5; }
.tot-table .lbl { color:#888; }
.tot-table .val { text-align:right; font-weight:500; color:#333; }
.tot-table .net-row td { border-top:2px solid #B1083C; color:#B1083C; font-weight:bold; font-size:12px; padding-top:6px; }
.tot-table .paid-row td { color:#197a3e; }
.tot-table .bal-row  td { color:#b91c1c; font-weight:bold; }

/* ── Footer ── */
.footer { margin-top:20px; text-align:center; font-size:10px; color:#bbb; border-top:1px solid #eee; padding-top:10px; }
.footer .msg { color:#777; font-size:10.5px; margin-bottom:4px; }
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
<div class="header">
    <img src="{{ public_path('images/dmd-logo.png') }}" alt="DMD">
    <div class="clinic-name">D.M.D Aesthetic, Dental &amp; Hair Transplant Clinic</div>
    <div class="clinic-sub">Super Market, F-6 Markaz Islamabad &nbsp;|&nbsp; 03335560507</div>
    <span class="inv-tag">Invoice #{{ $appointment->serial_series ?? $appointment->id }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</span>
</div>

{{-- Info --}}
<table class="info-row" cellpadding="0" cellspacing="0">
    <tr>
        <td class="info-col" style="padding-right:20px;">
            <div class="head">Patient</div>
            <table cellpadding="0" cellspacing="0">
                <tr><td class="lbl">Name</td><td class="val">{{ $patient->name ?? $appointment->name }}</td></tr>
                <tr><td class="lbl">Mobile</td><td class="val">{{ $appointment->phone ?? $patient->phone ?? '—' }}</td></tr>
                @if(!empty($patient->email))<tr><td class="lbl">Email</td><td class="val">{{ $patient->email }}</td></tr>@endif
            </table>
        </td>
        <td class="info-col" style="padding-left:20px;border-left:1px solid #f0f0f0;">
            <div class="head">Appointment</div>
            <table cellpadding="0" cellspacing="0">
                <tr><td class="lbl">Invoice No.</td><td class="val" style="font-weight:bold;color:#B1083C;">{{ $appointment->serial_series ?? $appointment->id }}</td></tr>
                <tr><td class="lbl">Date</td><td class="val">{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td></tr>
                @if($doctor)<tr><td class="lbl">Doctor</td><td class="val">{{ $doctor->name }}</td></tr>@endif
                <tr><td class="lbl">Payment</td>
                    <td class="val">
                        @if($appointment->is_paid === 'paid')
                            <span style="color:#197a3e;font-weight:bold;">● Paid</span>
                        @else
                            <span style="color:#b91c1c;font-weight:bold;">● Unpaid</span>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Services --}}
<table class="svc-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th style="width:32px;">#</th>
            <th>Service</th>
            <th class="right" style="width:110px;">List Price</th>
            <th class="right" style="width:110px;">Charged</th>
        </tr>
    </thead>
    <tbody>
        @forelse($services as $i => $sv)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $sv->name }}</td>
            <td class="right" style="color:#999;">{{ $currency }} {{ number_format($sv->price,2) }}</td>
            <td class="right" style="font-weight:500;">{{ $currency }} {{ number_format($sv->discounted_price ?? $sv->price,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#ccc;padding:14px;">No services recorded.</td></tr>
        @endforelse
    </tbody>
</table>

<hr class="divider">

{{-- Totals --}}
<table class="tot-table" cellpadding="0" cellspacing="0">
    <tr><td class="lbl">Gross Total</td><td class="val">{{ $currency }} {{ number_format($gross,2) }}</td></tr>
    @if($disc > 0)
    <tr><td class="lbl">Discount</td><td class="val" style="color:#b91c1c;">– {{ $currency }} {{ number_format($disc,2) }}</td></tr>
    @endif
    <tr class="net-row"><td class="lbl">Net Total</td><td class="val">{{ $currency }} {{ number_format($net,2) }}</td></tr>
    <tr class="paid-row"><td class="lbl">Paid</td><td class="val">{{ $currency }} {{ number_format($paid,2) }}</td></tr>
    <tr class="bal-row"><td class="lbl">Balance</td><td class="val">{{ $currency }} {{ number_format($bal,2) }}</td></tr>
</table>

<div class="footer">
    <div class="msg">{{ $footMsg }}</div>
    Generated {{ now()->format('d M Y, H:i') }}
</div>
</body>
</html>
