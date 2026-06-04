<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt {{ $appointment->serial_series ?? $appointment->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; font-size:11px; color:#1a1a2e; background:#fff; }

/* ── Top colour band ── */
.top-band { background:#B1083C; padding:16px 24px 0; }
.top-band table { width:100%; }
.logo-cell { width:50%; vertical-align:middle; }
.logo-cell img { max-height:70px; max-width:180px; }
.inv-cell { width:50%; vertical-align:middle; text-align:right; }
.inv-num  { font-size:28px; font-weight:bold; color:#fff; letter-spacing:2px; }
.inv-sub  { font-size:10px; color:#ffd6e0; margin-top:2px; }

/* ── White sub-band under logo ── */
.clinic-band { background:#fff; border-bottom:4px solid #B1083C; padding:6px 24px; }
.clinic-name { font-size:12px; font-weight:bold; color:#B1083C; }
.clinic-info { font-size:10px; color:#555; margin-top:2px; }

/* ── Body ── */
.body-wrap { padding:14px 24px; }

/* ── Two-col patient/appt block ── */
.block-table { width:100%; border-collapse:collapse; margin-bottom:14px; }
.block-cell { width:50%; vertical-align:top; padding:0 6px 0 0; }
.block-cell-right { padding:0 0 0 6px; border-left:2px solid #f0e0e4; }
.block-head { font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px;
              color:#fff; background:#B1083C; padding:3px 8px; margin-bottom:6px; }
.block-body td { font-size:10.5px; padding:2px 0; vertical-align:top; }
.block-body .lbl { color:#777; width:105px; }
.block-body .val { color:#1a1a2e; font-weight:500; }

/* ── Services ── */
.svc-wrap { border:1px solid #f0e0e4; border-radius:3px; overflow:hidden; margin-bottom:0; }
.svc-table { width:100%; border-collapse:collapse; }
.svc-table thead tr { background:#B1083C; }
.svc-table thead th { color:#fff; padding:6px 10px; font-size:10.5px; text-align:center; }
.svc-table thead th.left { text-align:left; }
.svc-table tbody tr:nth-child(even) { background:#fdf5f7; }
.svc-table tbody td { padding:5px 10px; font-size:10.5px; border-bottom:1px solid #f5e6ea; text-align:center; color:#1a1a2e; }
.svc-table tbody td.left { text-align:left; }

/* ── Totals ── */
.tot-table { width:100%; border-collapse:collapse; }
.tot-table tr td { padding:4px 10px; font-size:10.5px; border-bottom:1px solid #f5e6ea; }
.tot-table .lbl { text-align:right; color:#666; padding-right:20px; }
.tot-table .val { text-align:right; font-weight:bold; background:#fdf5f7; min-width:95px; }
.tot-net td { background:#B1083C !important; color:#fff !important; font-size:12px; font-weight:bold; }
.tot-paid td { color:#197a3e; }
.tot-bal  td { color:#b91c1c; font-weight:bold; }

/* ── Footer band ── */
.footer-band { background:#1a1a2e; color:#ccc; font-size:10px; text-align:center; padding:8px 24px; margin-top:14px; }
.footer-band span { color:#f0a0b8; }
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

{{-- Top band --}}
<div class="top-band">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td class="logo-cell"><img src="{{ public_path('images/dmd-logo.png') }}" alt="DMD"></td>
            <td class="inv-cell">
                <div class="inv-num">{{ $appointment->serial_series ?? ('#' . $appointment->id) }}</div>
                <div class="inv-sub">INVOICE NO. &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- Clinic sub-band --}}
<div class="clinic-band">
    <div class="clinic-name">D.M.D Aesthetic, Dental &amp; Hair Transplant Clinic</div>
    <div class="clinic-info">Super Market, F-6 Markaz Islamabad &nbsp;|&nbsp; ☎ 03335560507</div>
</div>

<div class="body-wrap">

    {{-- Patient + Appointment block --}}
    <table class="block-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="block-cell">
                <div class="block-head">Patient Details</div>
                <table class="block-body" cellpadding="0" cellspacing="0">
                    <tr><td class="lbl">Name</td><td class="val">{{ $patient->name ?? $appointment->name }}</td></tr>
                    <tr><td class="lbl">Mobile</td><td class="val">{{ $appointment->phone ?? $patient->phone ?? '—' }}</td></tr>
                    @if(!empty($patient->email))<tr><td class="lbl">Email</td><td class="val">{{ $patient->email }}</td></tr>@endif
                    <tr><td class="lbl">Patient Type</td><td class="val">{{ $appointment->is_paid === 'paid' ? 'Cash' : 'Credit' }}</td></tr>
                </table>
            </td>
            <td class="block-cell block-cell-right">
                <div class="block-head">Appointment Details</div>
                <table class="block-body" cellpadding="0" cellspacing="0">
                    <tr><td class="lbl">Invoice No.</td><td class="val" style="color:#B1083C;font-weight:bold;">{{ $appointment->serial_series ?? $appointment->id }}</td></tr>
                    <tr><td class="lbl">Date</td><td class="val">{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td></tr>
                    @if($doctor)<tr><td class="lbl">Doctor Ref.</td><td class="val" style="font-weight:bold;">{{ $doctor->name }}</td></tr>@endif
                    <tr><td class="lbl">Payment</td>
                        <td class="val">
                            @if($appointment->is_paid === 'paid')
                                <span style="background:#d1fae5;color:#065f46;padding:1px 6px;font-size:10px;font-weight:bold;">PAID</span>
                            @else
                                <span style="background:#fee2e2;color:#991b1b;padding:1px 6px;font-size:10px;font-weight:bold;">UNPAID</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Services --}}
    <div class="svc-wrap">
        <table class="svc-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:34px;">Sr.</th>
                    <th class="left">Service Name</th>
                    <th style="width:110px;">List Price</th>
                    <th style="width:110px;">Charged</th>
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
                <tr><td colspan="4" style="text-align:center;color:#aaa;padding:14px;">No services recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
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

<div class="footer-band">
    <span>{{ $footMsg }}</span> &nbsp;·&nbsp; Generated {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
