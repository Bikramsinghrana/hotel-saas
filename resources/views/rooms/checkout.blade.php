@extends('themes.hotel.layouts.app')

@section('title', 'Complete Booking | ' . ($room->post_title ?? 'Room Checkout'))

@push('styles')
<style>
.checkout-container {
    max-width: 1140px;
    margin: 3rem auto 5rem;
    padding: 0 1rem;
}
.checkout-card {
    background: #ffffff;
    border-radius: 1.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}
.checkout-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 2.5rem 2rem;
    color: #ffffff;
    position: relative;
}
.summary-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1.25rem;
    padding: 1.75rem;
    position: sticky;
    top: 2rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    color: #475569;
    font-size: 0.925rem;
}
.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px dashed #cbd5e1;
    font-weight: 800;
    font-size: 1.35rem;
    color: #0f172a;
}
.savings-callout {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1px solid #a7f3d0;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    color: #065f46;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}
.payment-option-card {
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.payment-option-card:hover {
    border-color: #10b981;
    background-color: #f0fdf4;
}
.payment-option-card input[type="radio"]:checked + .option-content {
    color: #065f46;
}
.btn-book-now {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: #ffffff;
    padding: 1rem 2rem;
    font-size: 1.15rem;
    font-weight: 700;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
}
.btn-book-now:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 14px 25px rgba(16, 185, 129, 0.35);
}
.user-status-pill {
    font-size: 0.825rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
}
.festival-offer-badge {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #fcd34d;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 700;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
</style>
@endpush

@section('content')
<!-- Checkout Hero Banner -->
<section class="checkout-banner">
    <div class="container text-center">
        <span class="badge bg-emerald-light text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold text-uppercase letter-spacing-1 mb-2">
            <i class="fas fa-lock me-1"></i> 256-Bit SSL Encrypted Checkout
        </span>
        <h1 class="h2 fw-bold font-serif mb-2">Finalize Your Reservation</h1>
        <p class="text-white-50 mb-0">Please review your stay details, apply any discounts or promo codes, and provide guest information.</p>
    </div>
</section>

<div class="checkout-container">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 p-3 mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 g-lg-5">
        <!-- Guest Details & Booking Form -->
        <div class="col-lg-7">
            <div class="checkout-card p-4 p-md-5">

                <!-- Auth Status Banner -->
                @auth
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light border border-success-subtle rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Logged in as {{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->email }} • Contact details auto-filled below</small>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                            <i class="fas fa-check-circle me-1"></i> Verified
                        </span>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light border border-secondary-subtle rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Guest Checkout</div>
                                <small class="text-muted">No account needed. Provide your details below for instant confirmation.</small>
                            </div>
                        </div>
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-sign-in-alt me-1"></i> Log In
                            </a>
                        @endif
                    </div>
                @endauth

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                        <span class="fw-bold">1</span>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark font-serif mb-0">Guest Information</h4>
                        <small class="text-muted">Booking voucher & invoice will be issued for this guest</small>
                    </div>
                </div>

                <form id="bookingForm" action="{{ route('rooms.book', $room->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">
                    <input type="hidden" name="quantity" id="formQuantity" value="{{ $quantity ?? 1 }}">
                    <input type="hidden" name="coupon_code" id="formCouponCode" value="{{ $effectiveCoupon ?? $calc['coupon_code'] ?? $couponCode ?? '' }}">
                    @if(!empty($extraServices))
                        @foreach((array)$extraServices as $serviceId)
                            <input type="hidden" name="extra_services[]" value="{{ is_array($serviceId) ? ($serviceId['id'] ?? '') : $serviceId }}">
                        @endforeach
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-dark">Primary Guest Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="customer_name" class="form-control" placeholder="e.g. John Doe" required value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required value="{{ old('email', auth()->user()->email ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 019-2834" required value="{{ old('phone', auth()->user()->phone ?? auth()->user()->details?->phone ?? '') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Special Requests (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Early check-in preference, quiet room, high floor, dietary requests, etc.">{{ old('notes') }}</textarea>
                            <small class="text-muted">Special requests are subject to availability upon check-in.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Payment Selection -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                            <span class="fw-bold">2</span>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark font-serif mb-0">Payment Method</h4>
                            <small class="text-muted">Choose your preferred settlement method</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="payment-option-card d-block h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="online" class="form-check-input mt-0" checked>
                                    <div class="option-content">
                                        <div class="fw-bold text-dark"><i class="fas fa-credit-card text-success me-1"></i> Pay Online</div>
                                        <small class="text-muted">Credit/Debit Card via Stripe</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="payment-option-card d-block h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="cash" class="form-check-input mt-0">
                                    <div class="option-content">
                                        <div class="fw-bold text-dark"><i class="fas fa-hotel text-success me-1"></i> Pay at Hotel</div>
                                        <small class="text-muted">Cash or card upon arrival</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4 border d-flex align-items-center gap-3">
                        <i class="fas fa-shield-check fa-2x text-success"></i>
                        <div class="small text-muted">
                            <strong>Guaranteed Safe Booking:</strong> Your transaction and personal details are encrypted and processed securely.
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitBooking" class="btn btn-book-now w-100">
                        <i class="fas fa-check-circle me-2"></i> Confirm & Complete Booking
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary Card / View Chart -->
        <div class="col-lg-5">
            <div class="summary-box">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h4 class="fw-bold text-dark font-serif mb-0">Booking Summary</h4>
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">View Chart</span>
                </div>
                
                <div class="d-flex align-items-center gap-3 mb-3">
                    @php
                        $roomImg = $room->media->first()?->path ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&auto=format&fit=crop';
                    @endphp
                    <img src="{{ $roomImg }}" alt="{{ $room->post_title }}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">{{ $room->post_title }}</h6>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            <span class="badge bg-success-subtle text-success small">{{ $room->roomType->room_type ?? 'Luxury Suite' }}</span>
                            @if($calc['discount_percent'] > 0)
                                <span class="festival-offer-badge">
                                    <i class="fas fa-gift"></i> {{ $calc['discount_percent'] }}% Festival Offer
                                </span>
                            @endif
                        </div>
                        <p class="small text-muted mb-0 mt-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ optional($room->hotel)->name ?? ($tenant->name ?? 'Our Grand Hotel') }}</p>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 border mb-3">
                    <div class="row text-center g-2">
                        <div class="col-6 border-end">
                            <span class="text-muted small d-block">Check-In</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($checkIn)->format('D, d M Y') }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Check-Out</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($checkOut)->format('D, d M Y') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Stay Details -->
                <div class="summary-row">
                    <span>Stay Duration</span>
                    <strong class="text-dark" id="summaryNights">{{ $calc['nights'] }} Night(s)</strong>
                </div>
                <div class="summary-row">
                    <span>Number of Rooms</span>
                    <strong class="text-dark" id="summaryQuantity">{{ $calc['quantity'] }} Room(s)</strong>
                </div>
                <div class="summary-row">
                    <span>Base Rate per Night</span>
                    <span id="summaryBasePrice">{{ \App\Helpers\CurrencyHelper::format($calc['base_price']) }}</span>
                </div>

                <!-- Room / Festival Discount -->
                @if($calc['discount_percent'] > 0)
                    <div class="summary-row text-success fw-semibold" id="roomDiscountRow">
                        <span><i class="fas fa-percent me-1"></i> Festival / Room Offer ({{ $calc['discount_percent'] }}% Off)</span>
                        <span id="roomDiscountVal">-{{ \App\Helpers\CurrencyHelper::format($calc['room_discount_amount']) }}</span>
                    </div>
                @else
                    <div class="summary-row text-success fw-semibold" id="roomDiscountRow" style="display: none;">
                        <span><i class="fas fa-percent me-1"></i> Festival / Room Offer</span>
                        <span id="roomDiscountVal">-₹0.00</span>
                    </div>
                @endif

                <div class="summary-row border-top pt-2 mt-2">
                    <span>Room Subtotal</span>
                    <span class="fw-semibold text-dark" id="summaryRoomTotal">{{ \App\Helpers\CurrencyHelper::format($calc['room_total']) }}</span>
                </div>

                <!-- Extra Services -->
                @if($calc['extra_total'] > 0)
                    <div class="summary-row" id="extraServicesRow">
                        <span>Extra Services</span>
                        <span class="text-dark" id="summaryExtraTotal">{{ \App\Helpers\CurrencyHelper::format($calc['extra_total']) }}</span>
                    </div>
                @else
                    <div class="summary-row" id="extraServicesRow" style="display: none;">
                        <span>Extra Services</span>
                        <span class="text-dark" id="summaryExtraTotal">₹0.00</span>
                    </div>
                @endif

                <!-- Coupon Discount -->
                <div class="summary-row text-success fw-bold" id="couponDiscountRow" style="display: {{ $calc['coupon_discount'] > 0 ? 'flex' : 'none' }};">
                    <span id="couponDiscountLabel"><i class="fas fa-tag me-1"></i> Coupon ({{ $calc['coupon_code'] ?? '' }})</span>
                    <span id="summaryCouponDiscount">-{{ \App\Helpers\CurrencyHelper::format($calc['coupon_discount']) }}</span>
                </div>

                <!-- Total Savings Badge -->
                <div class="savings-callout" id="savingsCallout" style="display: {{ $calc['total_discount'] > 0 ? 'flex' : 'none' }};">
                    <i class="fas fa-sparkles text-success"></i>
                    <span>Total Discount Saved: <strong id="totalDiscountVal">{{ \App\Helpers\CurrencyHelper::format($calc['total_discount']) }}</strong></span>
                </div>

                <!-- Taxes & GST (Individual Breakdown) -->
                <div id="taxSection" style="display: {{ ($calc['tax_amount'] ?? 0) > 0 ? 'block' : 'none' }};">
                    <div class="summary-row text-dark fw-semibold" id="taxRow">
                        <span><i class="fas fa-receipt text-primary me-1"></i> GST & Taxes (<span id="taxRateLabel">{{ $calc['tax_rate'] ?? 18 }}%</span>)</span>
                        <span id="summaryTaxAmount">+{{ \App\Helpers\CurrencyHelper::format($calc['tax_amount'] ?? 0) }}</span>
                    </div>
                    <div class="summary-row text-muted ps-3 small" id="cgstRow" style="font-size: 0.85rem;">
                        <span>↳ Central GST (CGST <span id="cgstRateLabel">{{ $calc['cgst_rate'] ?? 9 }}%</span>)</span>
                        <span class="text-dark fw-medium" id="summaryCgstAmount">+{{ \App\Helpers\CurrencyHelper::format($calc['cgst_amount'] ?? (($calc['tax_amount'] ?? 0) / 2)) }}</span>
                    </div>
                    <div class="summary-row text-muted ps-3 small" id="sgstRow" style="font-size: 0.85rem;">
                        <span>↳ State GST (SGST <span id="sgstRateLabel">{{ $calc['sgst_rate'] ?? 9 }}%</span>)</span>
                        <span class="text-dark fw-medium" id="summarySgstAmount">+{{ \App\Helpers\CurrencyHelper::format($calc['sgst_amount'] ?? (($calc['tax_amount'] ?? 0) / 2)) }}</span>
                    </div>
                </div>

                <!-- Total Amount Payable -->
                <div class="summary-total">
                    <span>Total Amount</span>
                    <span class="text-success" id="summaryTotalPayable">{{ \App\Helpers\CurrencyHelper::format($calc['total_payable']) }}</span>
                </div>

                <!-- Interactive Promo Code Section -->
                <div class="mt-4 pt-3 border-top">
                    <label class="form-label small fw-bold text-dark mb-2">
                        <i class="fas fa-tags text-primary me-1"></i> Promo / Coupon Code
                    </label>
                    <div class="input-group">
                        <input type="text" id="couponInput" class="form-control text-uppercase font-monospace" placeholder="e.g. SUMMER25" value="{{ $couponCode ?? $effectiveCoupon ?? $calc['coupon_code'] ?? '' }}">
                        <button class="btn btn-primary fw-semibold px-3" type="button" id="btnApplyCoupon">
                            <span id="applyCouponText">Apply</span>
                            <span id="applyCouponSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                    <div id="couponMessage" class="mt-2 small" style="display: {{ (!empty($effectiveCoupon ?? $calc['coupon_code']) && ($calc['coupon_discount'] ?? 0) > 0) || !empty($calc['coupon_error']) ? 'block' : 'none' }};">
                        @if(!empty($effectiveCoupon ?? $calc['coupon_code']) && ($calc['coupon_discount'] ?? 0) > 0)
                            <div class="d-flex align-items-center justify-content-between text-success fw-semibold bg-success-subtle p-2 rounded">
                                <span><i class="fas fa-check-circle me-1"></i> Applied: <strong>{{ $effectiveCoupon ?? $calc['coupon_code'] }}</strong></span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none fw-bold" id="btnRemoveCoupon">Remove</button>
                            </div>
                        @elseif(!empty($calc['coupon_error']))
                            <div class="d-flex align-items-center justify-content-between text-danger fw-semibold bg-danger-subtle p-2 rounded">
                                <span><i class="fas fa-exclamation-circle me-1"></i> {{ $calc['coupon_error'] }}</span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none fw-bold" id="btnRemoveCoupon">Clear</button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-center text-muted small">
                    <p class="mb-1"><i class="fas fa-check text-success me-1"></i> Free cancellation up to 48 hours prior</p>
                    <p class="mb-0"><i class="fas fa-check text-success me-1"></i> Taxes & service charges included</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const couponInput = document.getElementById('couponInput');
    const btnApplyCoupon = document.getElementById('btnApplyCoupon');
    const btnRemoveCoupon = document.getElementById('btnRemoveCoupon');
    const couponMessage = document.getElementById('couponMessage');
    const formCouponCode = document.getElementById('formCouponCode');
    const applyCouponText = document.getElementById('applyCouponText');
    const applyCouponSpinner = document.getElementById('applyCouponSpinner');

    const roomId = {{ $room->id }};
    const hotelId = {{ $room->hotel_id ?? 'null' }};
    const checkIn = "{{ $checkIn }}";
    const checkOut = "{{ $checkOut }}";
    const quantity = {{ $quantity ?? 1 }};
    const extraServices = @json($extraServices ?? []);

    function setCouponLoading(loading) {
        if (loading) {
            btnApplyCoupon.disabled = true;
            applyCouponText.classList.add('d-none');
            applyCouponSpinner.classList.remove('d-none');
        } else {
            btnApplyCoupon.disabled = false;
            applyCouponText.classList.remove('d-none');
            applyCouponSpinner.classList.add('d-none');
        }
    }

    function updateViewChart(calc, formatted, couponCode) {
        // Update Form hidden input
        if (formCouponCode) {
            formCouponCode.value = couponCode || '';
        }

        // Update Summary Breakdown
        if (formatted) {
            document.getElementById('summaryBasePrice').textContent = formatted.base_price;
            document.getElementById('summaryRoomTotal').textContent = formatted.room_total;
            document.getElementById('summaryTotalPayable').textContent = formatted.total_payable;

            // Room / Festival Discount
            const roomDiscountRow = document.getElementById('roomDiscountRow');
            if (calc.room_discount_amount > 0) {
                roomDiscountRow.style.display = 'flex';
                document.getElementById('roomDiscountVal').textContent = '-' + formatted.room_discount_amount;
            } else {
                roomDiscountRow.style.display = 'none';
            }

            // Coupon Discount
            const couponRow = document.getElementById('couponDiscountRow');
            if (calc.coupon_discount > 0 && (calc.coupon_code || couponCode)) {
                couponRow.style.display = 'flex';
                document.getElementById('couponDiscountLabel').innerHTML = '<i class="fas fa-tag me-1"></i> Coupon (' + (calc.coupon_code || couponCode) + ')';
                document.getElementById('summaryCouponDiscount').textContent = '-' + formatted.coupon_discount;
            } else {
                couponRow.style.display = 'none';
            }

            // Total Savings Callout
            const savingsCallout = document.getElementById('savingsCallout');
            if (calc.total_discount > 0) {
                savingsCallout.style.display = 'flex';
                document.getElementById('totalDiscountVal').textContent = formatted.total_discount;
            } else {
                savingsCallout.style.display = 'none';
            }

            // GST Taxes Section & Breakdown
            const taxSection = document.getElementById('taxSection');
            if (taxSection) {
                if (calc.tax_amount > 0) {
                    taxSection.style.display = 'block';
                    
                    const taxRateLabel = document.getElementById('taxRateLabel');
                    if (taxRateLabel) taxRateLabel.textContent = (calc.tax_rate || calc.gst_rate || 18) + '%';
                    
                    const summaryTax = document.getElementById('summaryTaxAmount');
                    if (summaryTax) summaryTax.textContent = '+' + (formatted.tax_amount || '₹0.00');

                    const cgstRateLabel = document.getElementById('cgstRateLabel');
                    if (cgstRateLabel) cgstRateLabel.textContent = (calc.cgst_rate || (calc.tax_rate ? calc.tax_rate / 2 : 9)) + '%';
                    
                    const summaryCgst = document.getElementById('summaryCgstAmount');
                    if (summaryCgst) summaryCgst.textContent = '+' + (formatted.cgst_amount || formatted.tax_amount || '₹0.00');

                    const sgstRateLabel = document.getElementById('sgstRateLabel');
                    if (sgstRateLabel) sgstRateLabel.textContent = (calc.sgst_rate || (calc.tax_rate ? calc.tax_rate / 2 : 9)) + '%';

                    const summarySgst = document.getElementById('summarySgstAmount');
                    if (summarySgst) summarySgst.textContent = '+' + (formatted.sgst_amount || formatted.tax_amount || '₹0.00');
                } else {
                    taxSection.style.display = 'none';
                }
            }
        }
    }

    function applyCouponCode(code) {
        code = (code || '').trim();
        if (!code) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Please enter a coupon code',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }

        setCouponLoading(true);

        const params = new URLSearchParams({
            code: code,
            room_id: roomId,
            hotel_id: hotelId || '',
            check_in: checkIn,
            check_out: checkOut,
            quantity: quantity
        });

        (extraServices || []).forEach(s => {
            const sid = (typeof s === 'object' && s !== null) ? s.id : s;
            if (sid) params.append('extra_services[]', sid);
        });

        fetch(`{{ route('api.coupons.validate') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            setCouponLoading(false);
            if (body.success && body.coupon) {
                couponInput.value = body.coupon.code;
                couponMessage.style.display = 'block';
                couponMessage.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between text-success fw-semibold bg-success-subtle p-2 rounded">
                        <span><i class="fas fa-check-circle me-1"></i> Applied: <strong>${body.coupon.code}</strong></span>
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none fw-bold" id="btnRemoveCouponDynamic">Remove</button>
                    </div>
                `;

                // Bind dynamic remove button
                document.getElementById('btnRemoveCouponDynamic')?.addEventListener('click', removeCouponCode);

                updateViewChart(body.calc, body.formatted, body.coupon.code);

                // Update URL query state to reflect coupon code
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('coupon', body.coupon.code);
                window.history.replaceState({}, '', newUrl.toString());

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: body.message || 'Coupon applied successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: body.message || 'Invalid coupon code',
                    showConfirmButton: false,
                    timer: 3500
                });
            }
        })
        .catch(err => {
            setCouponLoading(false);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Failed to validate coupon. Please try again.',
                showConfirmButton: false,
                timer: 3000
            });
        });
    }

    function removeCouponCode() {
        setCouponLoading(true);

        const params = new URLSearchParams({
            code: '__NONE__',
            room_id: roomId,
            hotel_id: hotelId || '',
            check_in: checkIn,
            check_out: checkOut,
            quantity: quantity
        });

        (extraServices || []).forEach(s => {
            const sid = (typeof s === 'object' && s !== null) ? s.id : s;
            if (sid) params.append('extra_services[]', sid);
        });

        fetch(`{{ route('api.coupons.validate') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(body => {
            setCouponLoading(false);
            couponInput.value = '';
            if (formCouponCode) formCouponCode.value = '';
            couponMessage.style.display = 'none';
            couponMessage.innerHTML = '';

            if (body.calc && body.formatted) {
                updateViewChart(body.calc, body.formatted, '');
            }

            // Remove coupon param from browser URL seamlessly
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete('coupon');
            window.history.replaceState({}, '', newUrl.toString());

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Coupon removed',
                showConfirmButton: false,
                timer: 2000
            });
        })
        .catch(err => {
            setCouponLoading(false);
            couponInput.value = '';
            if (formCouponCode) formCouponCode.value = '';
            couponMessage.style.display = 'none';
        });
    }

    btnApplyCoupon?.addEventListener('click', function() {
        applyCouponCode(couponInput.value);
    });

    couponInput?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyCouponCode(couponInput.value);
        }
    });

    btnRemoveCoupon?.addEventListener('click', removeCouponCode);
});
</script>
@endpush
