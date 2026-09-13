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
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.75rem;
    position: sticky;
    top: 2rem;
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
.form-floating label {
    color: #64748b;
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
        <p class="text-white-50 mb-0">Please review your stay details and provide guest information to confirm.</p>
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
        <!-- Guest Details & Form -->
        <div class="col-lg-7">
            <div class="checkout-card p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                        <span class="fw-bold">1</span>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark font-serif mb-0">Guest Information</h4>
                        <small class="text-muted">Booking confirmation will be sent to this email</small>
                    </div>
                </div>

                <form id="bookingForm" action="{{ route('rooms.book', $room->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-dark">Primary Guest Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="customer_name" class="form-control" placeholder="e.g. Alexander Hamilton" required value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="alex@domain.com" required value="{{ old('email', auth()->user()->email ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 234-5678" required value="{{ old('phone', auth()->user()->phone ?? '') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Special Requests (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Early check-in preference, dietary requirements, high floor room, etc.">{{ old('notes') }}</textarea>
                            <small class="text-muted">Special requests are subject to availability upon arrival.</small>
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
                            <small class="text-muted">Select how you prefer to settle your reservation</small>
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
                            <strong>Guaranteed Booking:</strong> Your credit card details and personal information are strictly encrypted and processed under PCI-DSS compliance.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-book-now w-100">
                        <i class="fas fa-check-circle me-2"></i> Confirm & Complete Booking
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="col-lg-5">
            <div class="summary-box shadow-sm">
                <h4 class="fw-bold text-dark font-serif mb-3 pb-2 border-bottom">Booking Summary</h4>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    @php
                        $roomImg = $room->media->first()?->path ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&auto=format&fit=crop';
                    @endphp
                    <img src="{{ $roomImg }}" alt="{{ $room->post_title }}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">{{ $room->post_title }}</h6>
                        <span class="badge bg-success-subtle text-success small">{{ $room->roomType->room_type ?? 'Luxury Suite' }}</span>
                        <p class="small text-muted mb-0 mt-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ optional($room->hotel)->name ?? ($tenant->name ?? 'Our Grand Hotel') }}</p>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-3 border mb-3">
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

                <div class="summary-row">
                    <span>Stay Duration</span>
                    <strong class="text-dark">{{ $calc['nights'] }} Night(s)</strong>
                </div>
                <div class="summary-row">
                    <span>Number of Rooms</span>
                    <strong class="text-dark">{{ $calc['quantity'] }} Room(s)</strong>
                </div>
                <div class="summary-row">
                    <span>Rate per Night</span>
                    <span>{{ \App\Helpers\CurrencyHelper::format($calc['discounted_price']) }}</span>
                </div>
                <div class="summary-row border-top pt-2 mt-2">
                    <span>Room Subtotal</span>
                    <span class="fw-semibold text-dark">{{ \App\Helpers\CurrencyHelper::format($calc['room_total']) }}</span>
                </div>

                @if($calc['extra_total'] > 0)
                    <div class="summary-row">
                        <span>Extra Services</span>
                        <span class="text-dark">{{ \App\Helpers\CurrencyHelper::format($calc['extra_total']) }}</span>
                    </div>
                @endif

                @if($calc['coupon_discount'] > 0)
                    <div class="summary-row text-success fw-bold">
                        <span><i class="fas fa-tag me-1"></i> Coupon Discount</span>
                        <span>-{{ \App\Helpers\CurrencyHelper::format($calc['coupon_discount']) }}</span>
                    </div>
                @endif

                <div class="summary-total">
                    <span>Total Amount</span>
                    <span class="text-success">{{ \App\Helpers\CurrencyHelper::format($calc['total_payable']) }}</span>
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
@endpush
