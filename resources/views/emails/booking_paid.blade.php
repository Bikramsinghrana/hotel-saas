<div style="font-family: Arial, sans-serif; color:#333;">
    <h2>Booking Confirmed</h2>
    <p>Hi {{ $order->customer_name }},</p>
    <p>Thank you for your booking. Your order <strong>{{ $order->order_number }}</strong> has been confirmed.</p>

    <p>
        <strong>Check-in:</strong> {{ \\Carbon\\Carbon::parse($order->start_date)->format('d M Y') }}<br>
        <strong>Check-out:</strong> {{ \\Carbon\\Carbon::parse($order->end_date)->format('d M Y') }}<br>
        <strong>Total:</strong> {{ \\App\\Helpers\\CurrencyHelper::format($order->total_amount) }}
    </p>

    @if($invoice)
        <p>Your invoice number: <strong>{{ $invoice->invoice_number }}</strong></p>
    @endif

    <p>If you have any questions, reply to this email.</p>

    <p>Best regards,<br>Hotel Team</p>
</div>
