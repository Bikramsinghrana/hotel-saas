<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; }
        .section { margin-top: 20px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:8px; border:1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice</h1>
        <div>{{ $invoice->invoice_number }}</div>
    </div>

    <div class="section">
        <strong>Customer</strong>
        <div>{{ $order->customer_name ?? 'N/A' }}</div>
        <div>{{ $order->email ?? '' }}</div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Booking {{ $order->order_number ?? $order->id }}</td>
                    <td style="text-align:right;">{{ number_format($invoice->amount,2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td style="text-align:right;">{{ number_format($invoice->tax_amount,2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td style="text-align:right;"><strong>{{ number_format($invoice->total_amount,2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <small>Payment Method: {{ $payment->gateway ?? $payment->payment_method ?? 'N/A' }}</small>
    </div>
</body>
</html>
