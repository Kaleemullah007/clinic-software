<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .header { background: linear-gradient(90deg,#B1083C,#d13729); color: #fff; padding: 24px 28px; }
    .header h2 { margin: 0; font-size: 20px; }
    .header p { margin: 4px 0 0; opacity: .85; font-size: 13px; }
    .body { padding: 28px; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    .info-row:last-child { border: none; }
    .items-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
    .items-table th { background: #f8f8f8; padding: 8px 10px; text-align: left; border-bottom: 2px solid #eee; }
    .items-table td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; }
    .total-row td { font-weight: bold; background: #fef2f2; color: #B1083C; }
    .footer { background: #f8f8f8; padding: 16px 28px; font-size: 12px; color: #888; text-align: center; }
    .badge-paid { background: #10b981; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
    .badge-unpaid { background: #f59e0b; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Order Confirmation</h2>
        <p>{{ $order->clinic?->name ?? config('app.name') }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $order->patient?->name ?? 'Customer' }}</strong>,</p>
        <p>Thank you for your order. Here are your order details:</p>

        <div style="background:#f9f9f9;border-radius:6px;padding:12px 16px;margin:16px 0">
            <div class="info-row"><span>Order #</span><strong>{{ $order->order_number }}</strong></div>
            <div class="info-row"><span>Date</span><span>{{ $order->created_at->format('d M Y, h:i A') }}</span></div>
            <div class="info-row"><span>Order Type</span><span>{{ ucfirst($order->order_type ?? 'Takeaway') }}</span></div>
            <div class="info-row"><span>Payment</span>
                <span class="{{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            @if($order->delivery_address)
            <div class="info-row"><span>Delivery Address</span><span>{{ $order->delivery_address }}</span></div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Unit Price</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variation_name)<br><small style="color:#888">{{ $item->variation_name }}</small>@endif
                    </td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @if($order->discount > 0)
                <tr><td colspan="3" style="text-align:right;color:#ef4444">Discount</td><td style="text-align:right;color:#ef4444">— {{ number_format($order->discount, 2) }}</td></tr>
                @endif
                @if($order->tax_amount > 0)
                <tr><td colspan="3" style="text-align:right">{{ $order->tax_label ?? 'Tax' }}</td><td style="text-align:right">{{ number_format($order->tax_amount, 2) }}</td></tr>
                @endif
                <tr class="total-row"><td colspan="3" style="text-align:right;padding:8px 10px">Grand Total</td><td style="text-align:right;padding:8px 10px">{{ number_format($order->grand_total, 2) }}</td></tr>
            </tfoot>
        </table>

        @if($order->notes)
        <p style="color:#666;font-size:13px"><strong>Notes:</strong> {{ $order->notes }}</p>
        @endif

        <p style="margin-top:20px;color:#666;font-size:13px">
            If you have any questions, please contact us.<br>
            Thank you for choosing {{ $order->clinic?->name ?? config('app.name') }}.
        </p>
    </div>
    <div class="footer">
        This is an automated email. Please do not reply directly to this message.
    </div>
</div>
</body>
</html>
