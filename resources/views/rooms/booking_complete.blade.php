@extends('themes.hotel.layouts.app')

@section('title', 'Booking Confirmation | ' . ($order->order_number ?? 'Completed'))

@section('content')

<style>
    .booking-wrapper{
        max-width: 760px;
        margin: 4rem auto;
        padding: 0 1.5rem;
    }

    .booking-card{
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15,23,42,.08);
        border: 1px solid #e2e8f0;
    }

    .booking-header{
        padding: 3rem 2rem;
        text-align: center;
        background: linear-gradient(
            135deg,
            var(--primary),
            var(--primary-dark)
        );
        color: #fff;
    }

    .success-icon{
        width: 90px;
        height: 90px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        background: rgba(255,255,255,.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        border: 3px solid rgba(255,255,255,.2);
    }

    .booking-header h1{
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        margin-bottom: .75rem;
    }

    .booking-header p{
        opacity: .9;
        max-width: 500px;
        margin: auto;
        line-height: 1.6;
    }

    .booking-body{
        padding: 2rem;
    }

    .booking-number{
        text-align: center;
        margin-bottom: 2rem;
    }

    .booking-number span{
        display: block;
        color: var(--text-muted);
        font-size: .9rem;
        margin-bottom: .5rem;
    }

    .booking-number strong{
        font-size: 1.7rem;
        color: var(--dark-bg);
        letter-spacing: 1px;
    }

    .summary-box{
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-grid{
        display: grid;
        grid-template-columns: repeat(2,1fr);
        gap: 1.5rem;
    }

    .summary-item small{
        display: block;
        color: var(--text-muted);
        margin-bottom: .4rem;
    }

    .summary-item strong{
        color: var(--dark-bg);
        font-size: 1rem;
    }

    .status-badge{
        display: inline-flex;
        align-items: center;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: #fef3c7;
        color: #92400e;
        font-size: .85rem;
        font-weight: 600;
    }

    .amount{
        color: var(--primary);
        font-size: 1.3rem;
        font-weight: 700;
    }

    .payment-form{
        margin-bottom: 1rem;
    }

    .pay-btn{
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 12px;
        background: var(--primary);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s ease;
    }

    .pay-btn:hover{
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .pay-later{
        margin-top: 1.5rem;
        background: var(--primary-light);
        border: 1px solid #bbf7d0;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        color: #166534;
        text-align: center;
        line-height: 1.6;
    }

    .support-note{
        margin-top: 1.5rem;
        text-align: center;
        color: var(--text-muted);
        font-size: .9rem;
    }

    @media (max-width: 640px){

        .booking-header{
            padding: 2.5rem 1.5rem;
        }

        .booking-body{
            padding: 1.5rem;
        }

        .summary-grid{
            grid-template-columns: 1fr;
        }

        .booking-header h1{
            font-size: 1.7rem;
        }

        .booking-number strong{
            font-size: 1.4rem;
        }
    }
</style>

<div class="booking-wrapper">

    <div class="booking-card">

        <div class="booking-header">

            <div class="success-icon">
                ✓
            </div>

            <h1>Booking Created Successfully</h1>

            <p>
                Thank you for choosing us. Your reservation has been created
                successfully and is awaiting payment confirmation.
            </p>

        </div>

        <div class="booking-body">

            <div class="booking-number">
                <span>Booking Reference</span>

                <strong>
                    #{{ $order->order_number }}
                </strong>
            </div>

            <div class="summary-box">
                @php
                    $extraInfo = [];
                    if (!empty($order->extra_info)) {
                        $extraInfo = is_array($order->extra_info) ? $order->extra_info : json_decode($order->extra_info, true);
                    }
                    $room = $order->room ?? null;
                    $roomQty = $extraInfo['room_qty'] ?? 1;
                    $nights = $order->total_nights ?? 1;
                    $basePrice = $extraInfo['base_price'] ?? ($room->price_per_day ?? 0);
                    $discountPercent = $extraInfo['discount_percent'] ?? ($room->discount ?? 0);
                    $roomDiscountAmount = $extraInfo['room_discount_amount'] ?? 0;
                    $couponCode = $extraInfo['coupon_code'] ?? null;
                    $couponDiscount = $extraInfo['coupon_discount'] ?? 0;
                    $totalDiscount = $extraInfo['total_discount'] ?? ($order->discount_amount ?? 0);
                @endphp

                <div class="summary-grid mb-3">
                    <div class="summary-item">
                        <small>Guest Name</small>
                        <strong>{{ $order->customer_name }}</strong>
                    </div>

                    <div class="summary-item">
                        <small>Email & Phone</small>
                        <strong>{{ $order->email ?? 'N/A' }} <br><span style="font-size:0.85rem; color:#64748b;">{{ $order->phone ?? '' }}</span></strong>
                    </div>

                    <div class="summary-item">
                        <small>Room & Hotel</small>
                        <strong>{{ $room->post_title ?? 'Room Stay' }}</strong>
                        <div style="font-size:0.8rem; color:#64748b;">{{ $order->hotel->name ?? 'Grand Hotel' }}</div>
                    </div>

                    <div class="summary-item">
                        <small>Stay Schedule</small>
                        <strong>{{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M') : '' }} &rarr; {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M Y') : '' }}</strong>
                        <div style="font-size:0.8rem; color:#64748b;">{{ $nights }} Night(s), {{ $roomQty }} Room(s)</div>
                    </div>

                    <div class="summary-item">
                        <small>Payment Status</small>
                        @php $status = $order->payment_status ?? $order->status ?? 'pending'; @endphp
                        <span class="status-badge" id="statusBadge">
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    <div class="summary-item">
                        <small>Payment Method</small>
                        <strong>{{ ucfirst($order->payment_method ?? 'Online') }}</strong>
                    </div>
                </div>

                @if($totalDiscount > 0)
                    <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1rem; font-size:0.875rem; color:#065f46;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span><i class="fas fa-gift me-1"></i> <strong>Discounts Applied:</strong></span>
                            <span>-{{ \App\Helpers\CurrencyHelper::format($totalDiscount) }}</span>
                        </div>
                        @if($roomDiscountAmount > 0)
                            <div style="font-size:0.8rem; margin-top:3px; opacity:0.9;">
                                &bull; Festival/Room Offer ({{ $discountPercent }}% off): -{{ \App\Helpers\CurrencyHelper::format($roomDiscountAmount) }}
                            </div>
                        @endif
                        @if($couponDiscount > 0)
                            <div style="font-size:0.8rem; margin-top:2px; opacity:0.9;">
                                &bull; Coupon ({{ $couponCode }}): -{{ \App\Helpers\CurrencyHelper::format($couponDiscount) }}
                            </div>
                        @endif
                    </div>
                @endif

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:0.75rem;">
                    <span style="font-weight:600; color:#475569;">Total Amount:</span>
                    <div class="amount" style="font-size:1.4rem;">
                        {{ \App\Helpers\CurrencyHelper::format($order->total_amount) }}
                    </div>
                </div>
            </div>


            <form method="POST"
                      action="{{ route('payments.checkout', $order->id) }}"
                      class="payment-form" id="payNowForm">

                    @csrf

                    @if(($order->payment_status ?? '') === 'paid')
                        <div style="display:flex; gap:0.5rem;">
                            <a href="/" class="pay-btn" style="background:#6b7280;">Go to Home</a>
                            <a href="{{ route('customer.dashboard') }}" class="pay-btn" style="background:#10b981;">My Dashboard</a>
                        </div>

                        @php $invoice = \App\Models\Invoice::where('room_order_id', $order->id)->first(); @endphp
                        @if($invoice)
                            <div style="margin-top:1rem;">
                                <a href="{{ route('payments.invoice.download', $invoice->id) }}" class="btn btn-outline-primary">Download Invoice</a>
                            </div>
                        @endif

                    @else

                    <button type="submit" class="pay-btn">
                        Proceed to Secure Payment
                    </button>

                    @endif

                </form>

                <div style="margin-top:1rem; display:flex; gap:0.5rem;">
                    <button id="checkStatusBtn" class="btn btn-light">Check Payment Status</button>
                    <a href="javascript:location.reload()" class="btn btn-light">Refresh</a>
                </div>

                <div class="pay-later">
                    Prefer to pay later? You can complete your payment
                    at the hotel during check-in.
                </div>

            <div class="support-note">
                Need assistance? Please contact our support team for any
                booking-related queries.
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const btn = document.getElementById('checkStatusBtn');
            if (!btn) return;
            btn.addEventListener('click', function(){
                fetch("{{ route('payments.order.status', $order->id) }}", { headers: { 'X-Requested-With':'XMLHttpRequest' } })
                    .then(r => r.json()).then(data => {
                        if (data.payment_status === 'paid' || (data.payment && data.payment.status === 'paid')){
                            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Payment confirmed', timer:2000, showConfirmButton:false });
                            // reload to show invoice/download links
                            setTimeout(()=> location.reload(), 800);
                        } else if (data.payment_status === 'failed' || (data.payment && data.payment.status === 'failed')){
                            Swal.fire({ toast:true, position:'top-end', icon:'error', title:'Payment failed', timer:3000, showConfirmButton:false });
                        } else {
                            Swal.fire({ toast:true, position:'top-end', icon:'info', title:'Payment pending', timer:2500, showConfirmButton:false });
                        }
                    }).catch(err => {
                        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'Could not check status', timer:3000, showConfirmButton:false });
                    });
            });
        });
    </script>
@endpush