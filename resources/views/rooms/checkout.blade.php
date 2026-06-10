@extends('layouts.app')

@push('styles')
<style>
.checkout-wrap { max-width: 1000px; margin: 4rem auto; padding: 0 1.5rem; }
.checkout-card { background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
.checkout-header { background: #0f172a; color: #fff; padding: 2rem; }
.checkout-header h2 { font-family: 'Playfair Display', serif; font-size: 2rem; margin: 0; }
.checkout-body { padding: 2rem; display: grid; grid-template-columns: 1fr 350px; gap: 3rem; }
@media(max-width:768px){ .checkout-body { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
.form-control { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 8px; outline: none; transition: border-color .2s; }
.form-control:focus { border-color: #16a34a; }

.summary-card { background: #f8fafc; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; }
.summary-title { font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem; }
.summary-item { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.9rem; color: #475569; }
.summary-total { display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1rem; border-top: 2px dashed #cbd5e1; font-weight: 800; font-size: 1.25rem; color: #0f172a; }

.btn-submit { width: 100%; padding: 1rem; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: background .2s; margin-top: 1.5rem; }
.btn-submit:hover { background: #15803d; }
</style>
@endpush

@section('content')

<div class="checkout-wrap">
    <div class="checkout-card">
        <div class="checkout-header">
            <h2>Complete Your Booking</h2>
            <p class="mb-0 text-white-50 mt-2">Almost there! Please enter your details below.</p>
        </div>

        <div class="checkout-body">
            <div>
                <h4 class="mb-4">Guest Details</h4>
                <form id="bookingForm" action="{{ route('rooms.book.ajax', $room->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="+1 234 567 8900">
                    </div>

                    <div class="form-group">
                        <label>Special Requests (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any special requests or notes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <div>
                            <label style="margin-right:12px;"><input type="radio" name="payment_method" value="online" checked> Pay Online</label>
                            <label><input type="radio" name="payment_method" value="cash"> Pay at Hotel (Cash)</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Confirm Booking</button>
                </form>
            </div>

            <div>
                <div class="summary-card">
                    <h3 class="summary-title">Booking Summary</h3>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-1">{{ $room->post_title }}</h5>
                        <p class="small text-muted mb-0"><i class="fas fa-map-marker-alt"></i> {{ optional($room->hotel)->name ?? 'Our Hotel' }}</p>
                    </div>

                    <div class="summary-item">
                        <span>Check-in</span>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Check-out</span>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Rooms</span>
                        <span class="fw-bold">{{ $calc['quantity'] }} Room(s)</span>
                    </div>
                    <div class="summary-item">
                        <span>Stay</span>
                        <span class="fw-bold">{{ $calc['nights'] }} Night(s)</span>
                    </div>
                    
                    <div class="summary-item mt-4 pt-3 border-top">
                        <span>Room Subtotal</span>
                        <span>{{ \App\Helpers\CurrencyHelper::format($calc['room_total']) }}</span>
                    </div>
                    @if($calc['extra_total'] > 0)
                        <div class="summary-item">
                            <span>Extra Services</span>
                            <span>{{ \App\Helpers\CurrencyHelper::format($calc['extra_total']) }}</span>
                        </div>
                    @endif
                    @if($calc['coupon_discount'] > 0)
                        <div class="summary-item text-success fw-bold">
                            <span>Coupon Discount</span>
                            <span>-{{ \App\Helpers\CurrencyHelper::format($calc['coupon_discount']) }}</span>
                        </div>
                    @endif

                    <div class="summary-total">
                        <span>Total Amount</span>
                        <span>{{ \App\Helpers\CurrencyHelper::format($calc['total_payable']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>window.STRIPE_KEY = '{{ config('services.stripe.key') }}';</script>
    <script src="/js/payment.js"></script>
@endpush
