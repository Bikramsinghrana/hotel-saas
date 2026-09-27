<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number ?? ('INV-' . $order->order_number) }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.45;
            padding: 15px;
            background: #ffffff;
            margin: 0;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .invoice-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }
        .hotel-brand {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .hotel-meta {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }
        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-pending {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.items-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11.5px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #16a34a;
        }
        .total-box {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .total-box td {
            padding: 4px 10px;
            border: none;
            font-size: 11.5px;
        }
        .total-box tr.final-total td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            padding: 8px 10px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            text-align: center;
            color: #64748b;
            font-size: 10.5px;
        }
        .print-btn-bar {
            background: #f1f5f9;
            padding: 10px;
            text-align: right;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print print-btn-bar">
        <button onclick="window.print()" style="padding: 6px 14px; background: #0f172a; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Print Invoice
        </button>
        <a href="{{ route('admin.bookings.show', $order->id) }}" style="padding: 6px 14px; background: #e2e8f0; color: #0f172a; text-decoration: none; border-radius: 4px; font-weight: bold; margin-left: 8px;">
            ← Back to Booking
        </a>
    </div>

    @php
        $extraInfo = [];
        if (!empty($order->extra_info)) {
            $extraInfo = is_array($order->extra_info) ? $order->extra_info : json_decode($order->extra_info, true);
        } elseif (!empty($order->payment_response)) {
            $decoded = json_decode($order->payment_response, true);
            if (is_array($decoded)) $extraInfo = $decoded;
        }

        $hotel = $order->hotel ?? optional($order->room)->hotel ?? null;
        $tenant = $order->tenant ?? optional($hotel)->tenant ?? tenant();

        $hotelName = $hotel->name ?? ($tenant->name ?? 'Grand Luxury Hotel');
        $hotelAddress = is_array($hotel?->address) ? implode(', ', array_filter($hotel->address)) : ($hotel?->address ?? ($tenant->address ?? ''));
        $hotelPhone = $hotel->phone ?? ($hotel->contact ?? ($tenant->phone ?? ''));
        $hotelEmail = $hotel->email ?? ($tenant->email ?? '');

        $room = $order->room ?? null;
        $roomTitle = $room->post_title ?? ($room->room_type ?? 'Luxury Suite Accommodation');
        $roomType = $room->roomType->room_type ?? ($room->room_type ?? 'Hotel Stay');

        $quantity = $extraInfo['room_qty'] ?? ($extraInfo['quantity'] ?? 1);
        $nights = $order->total_nights ?? max(1, \Carbon\Carbon::parse($order->start_date)->diffInDays(\Carbon\Carbon::parse($order->end_date)));
        $basePrice = $extraInfo['base_price'] ?? ($room->price_per_day ?? ($order->sub_total / max(1, $quantity * $nights)));
        $discountPercent = $extraInfo['discount_percent'] ?? ($room->discount ?? 0);
        $roomDiscountAmount = $extraInfo['room_discount_amount'] ?? 0;
        $couponCode = $extraInfo['coupon_code'] ?? null;
        $couponDiscount = $extraInfo['coupon_discount'] ?? 0;
        $totalDiscount = $extraInfo['total_discount'] ?? ($order->discount_amount ?? 0);
        $extraServices = $extraInfo['extra_services'] ?? ($extraInfo['extra_services_list'] ?? []);

        $paymentStatus = strtolower($order->payment_status ?? 'paid');
    @endphp

    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="invoice-title">Tax Invoice</div>
                <div class="invoice-subtitle">Invoice #: <strong>{{ $invoice->invoice_number ?? ('INV-' . $order->order_number) }}</strong></div>
                <div class="invoice-subtitle">Booking Ref: <strong>#{{ $order->order_number }}</strong></div>
                <div class="invoice-subtitle">Issued Date: <strong>{{ \Carbon\Carbon::parse($invoice->issued_at ?? $order->created_at ?? now())->format('d M Y, h:i A') }}</strong></div>
            </td>
            <td class="text-end" style="vertical-align: top;">
                <div class="hotel-brand">{{ $hotelName }}</div>
                @if($hotelAddress) <div class="hotel-meta">{{ $hotelAddress }}</div> @endif
                @if($hotelPhone) <div class="hotel-meta">Tel: {{ $hotelPhone }}</div> @endif
                @if($hotelEmail) <div class="hotel-meta">Email: {{ $hotelEmail }}</div> @endif
                <div style="margin-top: 6px;">
                    <span class="badge-status {{ $paymentStatus === 'paid' ? 'badge-paid' : ($paymentStatus === 'pending' ? 'badge-pending' : 'badge-cancelled') }}">
                        Payment: {{ strtoupper($paymentStatus) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-col" style="padding-right: 10px;">
                <div class="info-box">
                    <div class="info-title">Guest (Billed To)</div>
                    <div><strong>{{ $order->customer_name ?? 'Guest Customer' }}</strong></div>
                    @if(!empty($order->email)) <div>Email: {{ $order->email }}</div> @endif
                    @if(!empty($order->phone)) <div>Phone: {{ $order->phone }}</div> @endif
                    @if(!empty($order->address)) <div>Address: {{ $order->address }}</div> @endif
                    @if(!empty($order->city) || !empty($order->country)) 
                        <div>{{ implode(', ', array_filter([$order->city, $order->state, $order->country])) }} {{ $order->postcode }}</div>
                    @endif
                </div>
            </td>
            <td class="info-col" style="padding-left: 10px;">
                <div class="info-box">
                    <div class="info-title">Reservation Details</div>
                    <div><strong>Property:</strong> {{ $hotelName }}</div>
                    <div><strong>Check-In:</strong> {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('D, d M Y') : 'N/A' }}</div>
                    <div><strong>Check-Out:</strong> {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('D, d M Y') : 'N/A' }}</div>
                    <div><strong>Stay Period:</strong> {{ $nights }} Night(s) &bull; {{ $quantity }} Room(s)</div>
                    <div><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'Online / Cash') }}</div>
                    @if(!empty($order->transaction_id)) <div><strong>Transaction ID:</strong> {{ $order->transaction_id }}</div> @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description / Room Item</th>
                <th class="text-center" style="width: 18%;">Stay Schedule</th>
                <th class="text-end" style="width: 16%;">Rate / Night</th>
                <th class="text-end" style="width: 18%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $roomTitle }}</strong>
                    <div style="color: #64748b; font-size: 10.5px;">Category: {{ $roomType }}</div>
                </td>
                <td class="text-center">{{ $quantity }} Room(s) &times; {{ $nights }} Night(s)</td>
                <td class="text-end">{{ number_format($basePrice, 2) }}</td>
                <td class="text-end">{{ number_format($basePrice * $quantity * $nights, 2) }}</td>
            </tr>

            @if($discountPercent > 0 || $roomDiscountAmount > 0)
            <tr>
                <td colspan="3" class="text-success">
                    <strong>&bull; Festival / Room Special Discount ({{ $discountPercent }}% OFF)</strong>
                </td>
                <td class="text-end text-success">-{{ number_format($roomDiscountAmount, 2) }}</td>
            </tr>
            @endif

            @if(!empty($couponCode) && $couponDiscount > 0)
            <tr>
                <td colspan="3" class="text-success">
                    <strong>&bull; Promo / Coupon Code Discount ({{ $couponCode }})</strong>
                </td>
                <td class="text-end text-success">-{{ number_format($couponDiscount, 2) }}</td>
            </tr>
            @endif

            @if(!empty($extraServices) && is_array($extraServices))
                @foreach($extraServices as $svc)
                    <tr>
                        <td colspan="3">
                            &bull; Extra Service: <strong>{{ $svc['name'] ?? 'Service' }}</strong>
                        </td>
                        <td class="text-end">+{{ number_format((float)($svc['price'] ?? 0) * (int)$quantity, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td><strong>Room & Services Subtotal:</strong></td>
            <td class="text-end">{{ number_format($order->sub_total > 0 ? $order->sub_total : ($basePrice * $quantity * $nights), 2) }}</td>
        </tr>
        @if($totalDiscount > 0)
        <tr class="text-success">
            <td><strong>Total Discounts Saved:</strong></td>
            <td class="text-end">-{{ number_format($totalDiscount, 2) }}</td>
        </tr>
        @endif
        @if(($order->tax_amount ?? $invoice->tax_amount ?? 0) > 0)
        <tr>
            <td><strong>Taxes & Fees:</strong></td>
            <td class="text-end">{{ number_format($order->tax_amount ?? $invoice->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="final-total">
            <td><strong>Total Amount:</strong></td>
            <td class="text-end"><strong>{{ \App\Helpers\CurrencyHelper::format($order->total_amount ?? $invoice->total_amount ?? 0) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <div>Thank you for choosing <strong>{{ $hotelName }}</strong>! We wish you a delightful and memorable stay.</div>
        <div style="margin-top: 4px;">This is a computer generated invoice and does not require a physical signature.</div>
    </div>
</body>
</html>
