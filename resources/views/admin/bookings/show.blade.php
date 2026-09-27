@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Booking #' . $booking->order_number)

@push('styles')
<style>
.booking-detail-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.stay-timeline-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1rem 1.25rem;
}
.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
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
.badge-failed {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.itemized-table th {
    background: #f8fafc;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
}
.itemized-table td {
    vertical-align: middle;
    font-size: 0.9rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Header & Action Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-light text-dark border">ID: #{{ $booking->id }}</span>
            </div>
            <h1 class="page-title mb-0">Booking #{{ $booking->order_number }}</h1>
            <p class="text-muted small mb-0">Created on {{ $booking->created_at ? $booking->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Invoice Download Button -->
            <a href="{{ route('admin.bookings.invoice.download', $booking->id) }}" class="btn btn-primary d-flex align-items-center gap-2 fw-semibold shadow-sm">
                <i class="fas fa-file-pdf"></i>
                <span>Download Invoice</span>
            </a>

            <!-- Invoice Preview / Print Button -->
            <a href="{{ route('admin.bookings.invoice.preview', $booking->id) }}" target="_blank" class="btn btn-outline-dark d-flex align-items-center gap-2 fw-semibold">
                <i class="fas fa-print"></i>
                <span>Print / Preview</span>
            </a>

            <!-- Edit Booking -->
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-light border d-flex align-items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>

            <!-- Mark Paid if pending -->
            @if($booking->payment_status !== 'paid')
                <button type="button" class="btn btn-success d-flex align-items-center gap-2 fw-semibold row-action" data-action="mark-paid" data-url="{{ route('admin.bookings.mark-paid', $booking->id) }}">
                    <i class="fas fa-check-circle"></i>
                    <span>Mark Paid</span>
                </button>
            @endif

            <!-- Resend Confirmation -->
            <button type="button" class="btn btn-light border d-flex align-items-center gap-2 row-action" data-action="resend-email" data-url="{{ route('admin.bookings.resend-email', $booking->id) }}">
                <i class="fas fa-envelope"></i>
                <span>Resend Email</span>
            </button>
        </div>
    </div>

    @php
        $extraInfo = $booking->extra_info ?? [];
        if (empty($extraInfo) && !empty($booking->payment_response)) {
            $decoded = json_decode($booking->payment_response, true);
            if (is_array($decoded)) $extraInfo = $decoded;
        }

        $hotel = $booking->hotel ?? optional($booking->room)->hotel ?? null;
        $tenant = $booking->tenant ?? optional($hotel)->tenant ?? tenant();
        $room = $booking->room;

        $quantity = $extraInfo['room_qty'] ?? ($extraInfo['quantity'] ?? 1);
        $nights = $booking->total_nights ?? max(1, \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date)));
        $basePrice = $extraInfo['base_price'] ?? ($room->price_per_day ?? 0);
        $discountPercent = $extraInfo['discount_percent'] ?? ($room->discount ?? 0);
        $roomDiscountAmount = $extraInfo['room_discount_amount'] ?? 0;
        $couponCode = $extraInfo['coupon_code'] ?? null;
        $couponDiscount = $extraInfo['coupon_discount'] ?? 0;
        $totalDiscount = $extraInfo['total_discount'] ?? ($booking->discount_amount ?? 0);
        $extraServices = $extraInfo['extra_services'] ?? ($extraInfo['extra_services_list'] ?? []);

        $paymentStatus = strtolower($booking->payment_status ?? 'pending');
        $bookingStatus = strtolower($booking->status ?? 'pending');
    @endphp

    <div class="row g-4">
        <!-- Left Main Column (8 Cols) -->
        <div class="col-lg-8">
            <!-- Stay & Accommodation Card -->
            <div class="booking-detail-card">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-bed text-primary me-2"></i>Accommodation & Stay Schedule</h5>
                    <span class="badge {{ $bookingStatus === 'confirmed' ? 'bg-success' : ($bookingStatus === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }} px-3 py-2 text-uppercase">
                        {{ $bookingStatus }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3 mb-4">
                    @php
                        $roomImg = $room?->media?->first()?->path ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&auto=format&fit=crop';
                    @endphp
                    <img src="{{ $roomImg }}" alt="{{ $room->post_title ?? 'Room' }}" class="rounded-3 border shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $room->post_title ?? ($room->room_type ?? 'Standard Suite Room') }}</h4>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge bg-light text-secondary border">{{ $room->roomType->room_type ?? 'Deluxe Room' }}</span>
                            @if($discountPercent > 0)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    <i class="fas fa-gift me-1"></i> {{ $discountPercent }}% Room Offer
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stay Timeline Box -->
                <div class="stay-timeline-box mb-3">
                    <div class="row text-center g-2">
                        <div class="col-md-4 border-end">
                            <span class="text-muted small text-uppercase fw-bold d-block"><span class="timeline-dot bg-success me-1"></span> Check-In</span>
                            <h5 class="fw-bold text-dark mb-0 mt-1">{{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('D, d M Y') : 'N/A' }}</h5>
                        </div>
                        <div class="col-md-4 border-end">
                            <span class="text-muted small text-uppercase fw-bold d-block"><span class="timeline-dot bg-danger me-1"></span> Check-Out</span>
                            <h5 class="fw-bold text-dark mb-0 mt-1">{{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('D, d M Y') : 'N/A' }}</h5>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small text-uppercase fw-bold d-block">Duration & Rooms</span>
                            <h5 class="fw-bold text-primary mb-0 mt-1">{{ $nights }} Night(s) &bull; {{ $quantity }} Room(s)</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemized Pricing Breakdown Card -->
            <div class="booking-detail-card">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Itemized Bill & Discount Breakdown</h5>

                <div class="table-responsive mb-3">
                    <table class="table itemized-table align-middle">
                        <thead>
                            <tr>
                                <th>Item / Description</th>
                                <th class="text-center">Rate / Night</th>
                                <th class="text-center">Units</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ $room->post_title ?? 'Room Base Price' }}</strong>
                                    <div class="text-muted small">{{ $room->roomType->room_type ?? 'Accommodation' }}</div>
                                </td>
                                <td class="text-center">{{ \App\Helpers\CurrencyHelper::format($basePrice) }}</td>
                                <td class="text-center">{{ $quantity }} Room(s) × {{ $nights }} Night(s)</td>
                                <td class="text-end fw-semibold">{{ \App\Helpers\CurrencyHelper::format($basePrice * $quantity * $nights) }}</td>
                            </tr>

                            @if($discountPercent > 0 || $roomDiscountAmount > 0)
                                <tr class="table-success">
                                    <td colspan="3" class="text-success fw-semibold">
                                        <i class="fas fa-percentage me-1"></i> Festival / Room Special Discount ({{ $discountPercent }}% Off)
                                    </td>
                                    <td class="text-end text-success fw-bold">-{{ \App\Helpers\CurrencyHelper::format($roomDiscountAmount) }}</td>
                                </tr>
                            @endif

                            @if(!empty($couponCode) && $couponDiscount > 0)
                                <tr class="table-success">
                                    <td colspan="3" class="text-success fw-semibold">
                                        <i class="fas fa-tag me-1"></i> Coupon Code Applied (<strong>{{ $couponCode }}</strong>)
                                    </td>
                                    <td class="text-end text-success fw-bold">-{{ \App\Helpers\CurrencyHelper::format($couponDiscount) }}</td>
                                </tr>
                            @endif

                            @if(!empty($extraServices) && is_array($extraServices))
                                @foreach($extraServices as $svc)
                                    <tr>
                                        <td colspan="3">
                                            <i class="fas fa-plus-circle text-primary me-1"></i> Extra Service: <strong>{{ $svc['name'] ?? 'Service' }}</strong>
                                        </td>
                                        <td class="text-end fw-semibold">+{{ \App\Helpers\CurrencyHelper::format((float)($svc['price'] ?? 0) * (int)$quantity) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total Box -->
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Subtotal:</span>
                                <strong class="text-dark">{{ \App\Helpers\CurrencyHelper::format($booking->sub_total > 0 ? $booking->sub_total : ($basePrice * $quantity * $nights)) }}</strong>
                            </div>
                            @if($totalDiscount > 0)
                                <div class="d-flex justify-content-between mb-2 small text-success fw-semibold">
                                    <span>Total Discounts Saved:</span>
                                    <span>-{{ \App\Helpers\CurrencyHelper::format($totalDiscount) }}</span>
                                </div>
                            @endif
                            @if(($booking->tax_amount ?? 0) > 0)
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Taxes & GST:</span>
                                    <span>+{{ \App\Helpers\CurrencyHelper::format($booking->tax_amount) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <h5 class="fw-bold text-dark mb-0">Total Amount</h5>
                                <h4 class="fw-bold text-success mb-0">{{ \App\Helpers\CurrencyHelper::format($booking->total_amount) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Special Requests -->
            @if(!empty($booking->notes) || !empty($booking->special_request))
                <div class="booking-detail-card">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-comment-alt text-primary me-2"></i>Guest Requests & Notes</h5>
                    @if(!empty($booking->special_request))
                        <div class="mb-2">
                            <strong class="small text-muted d-block">Special Request:</strong>
                            <p class="mb-0 text-dark">{{ $booking->special_request }}</p>
                        </div>
                    @endif
                    @if(!empty($booking->notes))
                        <div>
                            <strong class="small text-muted d-block">Internal Notes:</strong>
                            <p class="mb-0 text-dark">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Sidebar (4 Cols) -->
        <div class="col-lg-4">
            <!-- Guest Profile Card -->
            <div class="booking-detail-card">
                <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-user-circle text-primary me-2"></i>Guest Information</h6>
                
                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Full Name</span>
                    <strong class="text-dark fs-6">{{ $booking->customer_name }}</strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Email Address</span>
                    <a href="mailto:{{ $booking->email }}" class="text-decoration-none text-primary fw-semibold">{{ $booking->email ?? 'N/A' }}</a>
                </div>

                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Phone Number</span>
                    <a href="tel:{{ $booking->phone }}" class="text-decoration-none text-dark fw-semibold">{{ $booking->phone ?? 'N/A' }}</a>
                </div>

                @if(!empty($booking->address) || !empty($booking->city))
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold d-block">Billing Address</span>
                        <div class="small text-secondary">
                            {{ $booking->address }}<br>
                            {{ implode(', ', array_filter([$booking->city, $booking->state, $booking->country])) }} {{ $booking->postcode }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Property / Hotel Card -->
            <div class="booking-detail-card">
                <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-hotel text-primary me-2"></i>Hotel Property</h6>
                <div class="fw-bold text-dark fs-6">{{ $hotel->name ?? ($tenant->name ?? 'Grand Luxury Hotel') }}</div>
                @if($hotel?->address)
                    <div class="text-muted small mt-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ is_array($hotel->address) ? implode(', ', array_filter($hotel->address)) : $hotel->address }}</div>
                @endif
                @if($hotel?->phone)
                    <div class="text-muted small mt-1"><i class="fas fa-phone text-muted me-1"></i> {{ $hotel->phone }}</div>
                @endif
            </div>

            <!-- Payment & Invoice Card -->
            <div class="booking-detail-card">
                <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-credit-card text-primary me-2"></i>Payment Details</h6>
                
                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Payment Status</span>
                    <span class="badge {{ $paymentStatus === 'paid' ? 'badge-paid' : ($paymentStatus === 'pending' ? 'badge-pending' : 'badge-failed') }} px-3 py-1 rounded-pill mt-1">
                        {{ strtoupper($paymentStatus) }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Payment Method</span>
                    <strong class="text-dark">{{ ucfirst($booking->payment_method ?? 'Cash / Counter') }}</strong>
                </div>

                @if($booking->transaction_id)
                    <div class="mb-3">
                        <span class="text-muted extra-small text-uppercase fw-bold d-block">Transaction ID</span>
                        <code class="text-dark small">{{ $booking->transaction_id }}</code>
                    </div>
                @endif

                <div class="mb-3">
                    <span class="text-muted extra-small text-uppercase fw-bold d-block">Official Invoice</span>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border font-monospace">{{ $invoice->invoice_number ?? ('INV-' . $booking->order_number) }}</span>
                    </div>
                </div>

                <a href="{{ route('admin.bookings.invoice.download', $booking->id) }}" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="fas fa-download me-1"></i> Download PDF Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.row-action').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const url = this.getAttribute('data-url');
            const original = this.innerHTML;

            if (!action || !url) return;

            const confirmMap = {
                'mark-paid': { title: 'Mark booking as paid?', text: 'This will mark payment as paid and send confirmation email.' },
                'resend-email': { title: 'Resend confirmation email?', text: 'This will resend the booking confirmation to the customer.' }
            };

            const conf = confirmMap[action] || { title: 'Proceed?', text: '' };

            Swal.fire({ 
                title: conf.title, 
                text: conf.text, 
                icon: 'question', 
                showCancelButton: true, 
                confirmButtonText: 'Yes, proceed',
                confirmButtonColor: '#2563eb'
            }).then(res => {
                if (!res.isConfirmed) return;

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';

                fetch(url, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Accept': 'application/json' 
                    } 
                }).then(async r => {
                    const data = await r.json().catch(() => null);
                    if (r.ok && data && data.status === 'success'){
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message || 'Updated successfully', showConfirmButton: false, timer: 2000 });
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data?.message || 'Action failed', showConfirmButton: false, timer: 3000 });
                        this.disabled = false;
                        this.innerHTML = original;
                    }
                }).catch(err => {
                    this.disabled = false;
                    this.innerHTML = original;
                });
            });
        });
    });
});
</script>
@endpush
