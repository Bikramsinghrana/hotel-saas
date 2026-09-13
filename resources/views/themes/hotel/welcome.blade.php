@extends('themes.hotel.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Grand Palace Resort') . ' | Luxury Hotels & Suites')

@push('styles')
<style>
/* ── HOTEL HERO ── */
.hero {
    position: relative;
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f2027 100%);
}
.hero-bg {
    position: absolute; inset: 0;
    background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&h=900&fit=crop&q=90');
    background-size: cover; background-position: center;
    opacity: 0.45;
}
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(15,23,42,0.55) 0%, rgba(15,23,42,0.8) 100%);
}
.hero-content {
    position: relative; z-index: 2;
    text-align: center;
    padding: 3rem 1.5rem 6rem;
    max-width: 860px;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(22,163,74,0.2); border: 1px solid rgba(22,163,74,0.5);
    color: #86efac; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.1em;
    text-transform: uppercase; padding: 0.4rem 1rem; border-radius: 99px;
    margin-bottom: 1.5rem;
}
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 6vw, 4.5rem);
    font-weight: 700; color: #fff; line-height: 1.15;
    margin-bottom: 1.25rem;
}
.hero-title span { color: #4ade80; }
.hero-subtitle {
    font-size: 1.15rem; color: #cbd5e1; max-width: 560px;
    margin: 0 auto 2.5rem; line-height: 1.7;
}
.hero-stats {
    display: flex; justify-content: center; gap: 2.5rem;
    flex-wrap: wrap; margin-bottom: 0;
}
.hero-stat { text-align: center; }
.hero-stat-num { font-size: 1.8rem; font-weight: 800; color: #fff; }
.hero-stat-lbl { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; }

/* ── SEARCH BAR ── */
.search-wrap {
    position: relative; z-index: 10;
    max-width: 1000px; margin: -2.5rem auto 0;
    padding: 0 1.5rem;
}
.search-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    padding: 1.5rem;
    display: grid; grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem; align-items: end;
}
@media(max-width:768px){ .search-card { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px){ .search-card { grid-template-columns: 1fr; } }
.search-field label {
    display: block; font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: #64748b; margin-bottom: 0.4rem;
}
.search-field input, .search-field select {
    width: 100%; padding: 0.65rem 0.9rem;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    font-size: 0.9rem; color: #1e293b; background: #f8fafc;
    outline: none; transition: border-color .2s;
}
.search-field input:focus, .search-field select:focus {
    border-color: var(--primary); background: #fff;
}
.search-btn {
    padding: 0.65rem 1.75rem; border-radius: 8px;
    font-size: 0.95rem; font-weight: 700; color: #fff;
    background: var(--primary); border: none; cursor: pointer;
    transition: all .2s; box-shadow: 0 4px 14px rgba(22,163,74,.35);
    white-space: nowrap; height: 42px;
}
.search-btn:hover { background: var(--primary-dark); transform: translateY(-1px); }

/* ── HOTEL CARDS ── */
.hotels-section { padding: 5rem 1.5rem 3rem; }
.section-inner { max-width: 1280px; margin: 0 auto; }
.section-header { text-align: center; margin-bottom: 3rem; }
.section-badge {
    display: inline-block; font-size: 0.75rem; font-weight: 700;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--primary); background: var(--primary-light);
    padding: 0.35rem 0.85rem; border-radius: 99px; margin-bottom: 0.75rem;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 3.5vw, 2.6rem);
    font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;
}
.section-subtitle { font-size: 1rem; color: #64748b; max-width: 520px; margin: 0 auto; line-height: 1.6; }

.hotels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.75rem;
}
.hotel-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e2e8f0; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.hotel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,.1);
}
.hotel-card-img {
    position: relative; height: 210px; overflow: hidden;
    background: #1e293b;
}
.hotel-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .35s;
}
.hotel-card:hover .hotel-card-img img { transform: scale(1.04); }
.hotel-rating-badge {
    position: absolute; top: 0.75rem; right: 0.75rem;
    background: rgba(15,23,42,0.85); backdrop-filter: blur(4px);
    color: #fbbf24; font-size: 0.8rem; font-weight: 700;
    padding: 0.3rem 0.65rem; border-radius: 99px;
    display: flex; align-items: center; gap: 0.25rem;
}
.hotel-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.hotel-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem; font-weight: 700; color: #0f172a;
    margin-bottom: 0.35rem;
}
.hotel-card-location {
    font-size: 0.825rem; color: #64748b;
    display: flex; align-items: center; gap: 0.3rem; margin-bottom: 1rem;
}
.hotel-card-footer {
    margin-top: auto; padding-top: 1rem; border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.hotel-price { font-size: 1.25rem; font-weight: 800; color: #0f172a; }
.hotel-price span { font-size: 0.8rem; font-weight: 400; color: #64748b; }
.btn-book {
    padding: 0.5rem 1.1rem; border-radius: 8px;
    font-size: 0.85rem; font-weight: 600; color: #fff;
    background: var(--primary); text-decoration: none;
    border: none; transition: background .2s;
}
.btn-book:hover { background: var(--primary-dark); }

/* ── WHY CHOOSE US ── */
.why-section {
    background: #f1f5f9; padding: 4.5rem 1.5rem;
}
.why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem; margin-top: 3rem;
}
.why-card {
    background: #fff; border-radius: 14px;
    padding: 1.75rem; text-align: center;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,.04);
}
.why-icon {
    width: 54px; height: 54px; border-radius: 12px;
    background: var(--primary-light); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin: 0 auto 1.25rem;
}
.why-card h4 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
.why-card p { font-size: 0.85rem; color: #64748b; line-height: 1.6; }
</style>
@endpush

@section('content')

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            ✦ {{ $tenant ? $tenant->name : 'Premier Hospitality SaaS' }}
        </div>
        <h1 class="hero-title">
            Find & Book Your <span>Perfect Stay</span>
        </h1>
        <p class="hero-subtitle">
            Experience luxury accommodations, boutique stays, and premier resorts with transparent pricing and instant confirmations.
        </p>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $hotels->count() > 0 ? $hotels->count() : '20+' }}</div>
                <div class="hero-stat-lbl">Properties</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">99.8%</div>
                <div class="hero-stat-lbl">Satisfaction</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">24/7</div>
                <div class="hero-stat-lbl">Concierge Support</div>
            </div>
        </div>
    </div>
</section>

<!-- SEARCH BAR -->
<x-room-filter />

<!-- HOTELS SHOWCASE -->
<section class="hotels-section">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-badge">Top Rated Stays</span>
            <h2 class="section-title">Featured Accommodations</h2>
            <p class="section-subtitle">Discover handcrafted luxury rooms and suites designed for maximum comfort.</p>
        </div>

        <div class="hotels-grid">
            @foreach($hotels as $hotel)
                <div class="hotel-card">
                    <div class="hotel-card-img">
                        @php
                            $img = ($hotel->media && $hotel->media->first()) 
                                ? (filter_var($hotel->media->first()->path, FILTER_VALIDATE_URL) ? $hotel->media->first()->path : asset($hotel->media->first()->path))
                                : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop';
                        @endphp
                        <img src="{{ $img }}" alt="{{ $hotel->name }}" loading="lazy">
                        <span class="hotel-rating-badge">★ {{ $hotel->rating ?? '4.8' }}</span>
                    </div>
                    <div class="hotel-card-body">
                        <h3 class="hotel-card-title">{{ $hotel->name }}</h3>
                        <div class="hotel-card-location">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ is_array($hotel->address) ? ($hotel->address['city'] ?? 'Scenic Resort Area') : 'Prime Waterfront Location' }}
                        </div>
                        <div class="hotel-card-footer">
                            <div class="hotel-price">
                                ₹{{ number_format($hotel->base_price ?? 3500, 0) }}
                                <span>/ night</span>
                            </div>
                            <a href="{{ route('rooms.index') }}" class="btn-book">
                                View Rooms &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="why-section">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-badge">Why Choose Us</span>
            <h2 class="section-title">The Standard in Modern Hospitality</h2>
            <p class="section-subtitle">Enjoy effortless bookings with verified properties and guest-first services.</p>
        </div>

        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-shield-alt"></i></div>
                <h4>Best Price Guarantee</h4>
                <p>We ensure you always get the lowest rates without hidden platform fees.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-concierge-bell"></i></div>
                <h4>24/7 Dedicated Concierge</h4>
                <p>Round-the-clock front desk and support team ready to assist your stay.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-bolt"></i></div>
                <h4>Instant Confirmation</h4>
                <p>Real-time room availability, instant invoice generation, and secure payments.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-star"></i></div>
                <h4>Verified Reviews</h4>
                <p>Authentic feedback from verified guests for confidence on every booking.</p>
            </div>
        </div>
    </div>
</section>

<!-- OFFERS & PRIVILEGES -->
@if(isset($offers) && $offers->count() > 0)
<section class="py-5" style="background: #0f172a; color: #fff;">
    <div class="section-inner py-3">
        <div class="section-header">
            <span class="badge bg-warning text-dark px-3 py-1 font-weight-bold text-uppercase">Exclusive Deals</span>
            <h2 class="section-title text-white mt-2">Member Discounts & Offers</h2>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach($offers as $offer)
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 rounded border border-secondary text-center" style="background: rgba(255,255,255,0.04); border-radius: 16px;">
                        <span class="badge bg-warning text-dark font-weight-bold mb-2">CODE: {{ $offer->code }}</span>
                        <h4 class="text-white font-weight-bold">{{ $offer->title ?? 'Complimentary Resort Credit' }}</h4>
                        <p class="text-muted small mb-0">{{ $offer->description ?? 'Enjoy premium dining discounts and free room upgrade.' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
