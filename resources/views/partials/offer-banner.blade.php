@php $style = $style ?? 'full'; @endphp

@if(isset($offers) && $offers->count() > 0)
    @if($style === 'full')
        <div class="offer-banner-grid">
            @foreach($offers as $offer)
                <div class="offer-card-v2">
                    <div class="offer-card-v2-img">
                        <img src="{{ $offer->image ? asset('storage/'.$offer->image) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&h=600&fit=crop' }}" 
                             alt="{{ $offer->title }}">
                        <div class="offer-card-v2-badge">
                            {{ $offer->discount_type == 'percentage' ? $offer->discount_value.'%' : \App\Helpers\CurrencyHelper::format($offer->discount_value) }} OFF
                        </div>
                    </div>
                    <div class="offer-card-v2-body">
                        <span class="offer-card-v2-category">{{ $offer->title }}</span>
                        <h3 class="offer-card-v2-title">{{ $offer->description }}</h3>
                        
                        @if($offer->code)
                            <div class="offer-card-v2-coupon">
                                <span>Code:</span>
                                <code>{{ $offer->code }}</code>
                            </div>
                        @endif

                        <div class="offer-card-v2-footer">
                            @if($offer->expire_date)
                                <div class="offer-card-v2-expiry">
                                    Expires: {{ $offer->expire_date->format('M d') }}
                                </div>
                            @endif
                            <a href="#rooms" class="btn-offer-book">Book Now</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($style === 'compact')
        <div class="offer-banner-section mb-4">
            @php $latest = $offers->first(); @endphp
            <div class="compact-offer-banner">
                <div class="compact-offer-info">
                    <div class="compact-offer-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="compact-offer-text">
                        <h5>{{ $latest->title }}</h5>
                        <p>Use code <strong>{{ $latest->code }}</strong> for {{ $latest->discount_type == 'percentage' ? $latest->discount_value.'%' : \App\Helpers\CurrencyHelper::format($latest->discount_value) }} discount!</p>
                    </div>
                </div>
                <div class="compact-offer-action">
                    @if($latest->code)
                        <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold" 
                                onclick="document.getElementById('coupon-code').value='{{ $latest->code }}'; applyCoupon();">
                            APPLY NOW
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
