@extends('layouts.app')

@push('styles')
<style>
/* ── HERO ── */
.hero {
    position: relative;
    min-height: 92vh;
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
    padding: 2rem 1.5rem 6rem;
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
@media(max-width:520px){ .search-card { grid-template-columns: 1fr; } }
.search-field label {
    display: block; font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 0.4rem;
}
.search-field input, .search-field select {
    width: 100%; padding: 0.7rem 0.85rem; border: 1.5px solid #e2e8f0;
    border-radius: 10px; font-size: 0.9rem; font-family: inherit;
    color: #1e293b; background: #f8fafc; outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.search-field input:focus, .search-field select:focus {
    border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
    background: #fff;
}
.btn-search {
    padding: 0.75rem 1.75rem; background: #16a34a; color: #fff;
    border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(22,163,74,0.35);
}
.btn-search:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22,163,74,.45); }

/* ── SECTION ── */
.section { padding: 6rem 1.5rem 3rem; max-width: 1280px; margin: 0 auto; }
.section-head { text-align: center; margin-bottom: 3rem; }
.section-kicker {
    display: inline-block; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: #16a34a; margin-bottom: 0.6rem;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 700;
    color: #0f172a; margin-bottom: 0.75rem;
}
.section-title span { color: #16a34a; }
.section-desc { color: #64748b; font-size: 1rem; max-width: 540px; margin: 0 auto; line-height: 1.7; }

/* ── HOTEL CARDS ── */
.hotels-grid { display: grid; gap: 1.75rem; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }

.hotel-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #f1f5f9; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: transform .3s, box-shadow .3s;
    display: flex; flex-direction: column;
}
.hotel-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.13); }

.hotel-img-wrap { position: relative; height: 220px; overflow: hidden; background: #e2e8f0; }
.hotel-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s; }
.hotel-card:hover .hotel-img-wrap img { transform: scale(1.07); }

.hotel-tag {
    position: absolute; top: 12px; left: 12px;
    background: #16a34a; color: #fff;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    padding: 0.3rem 0.7rem; border-radius: 6px;
}
.hotel-rating-badge {
    position: absolute; top: 12px; right: 12px;
    background: rgba(255,255,255,0.96); backdrop-filter: blur(6px);
    border-radius: 8px; padding: 0.3rem 0.6rem;
    display: flex; align-items: center; gap: 0.3rem;
    font-size: 0.8rem; font-weight: 700; color: #1e293b;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.star-icon { color: #f59e0b; font-size: 0.9rem; }

.hotel-body { padding: 1.25rem 1.4rem 1.4rem; flex-grow: 1; display: flex; flex-direction: column; }
.hotel-category { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #16a34a; margin-bottom: 0.35rem; }
.hotel-name { font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; line-height: 1.3; }
.hotel-location { display: flex; align-items: center; gap: 0.3rem; font-size: 0.82rem; color: #64748b; margin-bottom: 1rem; }
.hotel-desc { font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 1.25rem; flex-grow: 1; }

.hotel-stars { display: flex; gap: 2px; margin-bottom: 1rem; }
.hotel-stars .s { color: #f59e0b; font-size: 0.85rem; }
.hotel-stars .s.empty { color: #e2e8f0; }

.hotel-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
.hotel-price { }
.hotel-price-lbl { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
.hotel-price-num { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
.hotel-price-per { font-size: 0.78rem; color: #94a3b8; font-weight: 400; }
.btn-view {
    padding: 0.55rem 1.1rem; background: #0f172a; color: #fff;
    text-decoration: none; border-radius: 8px; font-size: 0.82rem;
    font-weight: 600; transition: background .2s, transform .15s;
}
.btn-view:hover { background: #16a34a; transform: translateY(-1px); }

/* ── DEMO BANNER ── */
.demo-banner {
    background: #fefce8; border: 1px solid #fde047; border-radius: 12px;
    padding: 1rem 1.25rem; margin-bottom: 2rem;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.875rem; color: #854d0e;
}

/* ── AMENITIES ── */
.amenities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: 1.5rem; margin-top: 3rem; }
.amenity-card {
    background: #fff; border-radius: 14px; padding: 1.75rem 1.5rem;
    border: 1px solid #f1f5f9; text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform .25s, box-shadow .25s;
}
.amenity-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.amenity-icon { font-size: 2.2rem; margin-bottom: 0.9rem; }
.amenity-name { font-weight: 700; color: #0f172a; margin-bottom: 0.35rem; font-size: 0.95rem; }
.amenity-desc { font-size: 0.8rem; color: #64748b; line-height: 1.5; }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <span>★</span> Trusted by 50,000+ travellers
        </div>
        <h1 class="hero-title">
            Experience Luxury<br><span>Like Never Before</span>
        </h1>
        <p class="hero-subtitle">
            Indulge in world-class amenities and exceptional service. Discover your perfect sanctuary and create memories that last a lifetime.
        </p>
        <div class="hero-stats">
            <div class="hero-stat"><div class="hero-stat-num">500+</div><div class="hero-stat-lbl">Hotels</div></div>
            <div class="hero-stat"><div class="hero-stat-num">50K+</div><div class="hero-stat-lbl">Happy Guests</div></div>
            <div class="hero-stat"><div class="hero-stat-num">4.9★</div><div class="hero-stat-lbl">Avg Rating</div></div>
            <div class="hero-stat"><div class="hero-stat-num">120+</div><div class="hero-stat-lbl">Cities</div></div>
        </div>
    </div>
</section>

{{-- ── FILTER / SEARCH BAR ── --}}
<x-room-filter />

{{-- ── OFFERS SECTION ── --}}
@if(isset($offers) && $offers->count() > 0)
<div class="section pt-5" id="offers">
    <div class="section-head mb-5">
        <div class="section-kicker">Exclusive Deals</div>
        <h2 class="section-title">Limited Time <span>Offers</span></h2>
        <p class="section-desc">Take advantage of our special events and seasonal discounts before they expire.</p>
    </div>

    <div class="row g-4">
        @foreach($offers as $offer)
            <div class="col-md-6 col-lg-4">
                <div class="offer-card position-relative overflow-hidden rounded-3 shadow-sm" style="height: 240px;">
                    <img src="{{ $offer->image ? asset('storage/'.$offer->image) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&h=600&fit=crop' }}" 
                         class="w-100 h-100 object-fit-cover transition-all" alt="{{ $offer->title }}">
                    <div class="position-absolute inset-0 bg-dark opacity-40"></div>
                    <div class="position-absolute inset-0 p-4 d-flex flex-column justify-content-end text-white">
                        <div class="badge bg-success align-self-start mb-2">
                            {{ $offer->discount_type == 'percentage' ? $offer->discount_value.'%' : \App\Helpers\CurrencyHelper::format($offer->discount_value) }} OFF
                        </div>
                        <h4 class="fw-bold mb-1">{{ $offer->title }}</h4>
                        <p class="small mb-0 opacity-90 text-truncate">{{ $offer->description }}</p>
                        @if($offer->code)
                            <div class="mt-2 small fw-bold">Use Code: <span class="text-warning">{{ $offer->code }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── HOTEL CARDS ── --}}
<div class="section" id="rooms">
    <div class="section-head">
        <div class="section-kicker">Our Signature Rooms</div>
        <h2 class="section-title">Discover <span>Premium</span> Stays</h2>
        <p class="section-desc">Experience unparalleled comfort and luxury in our carefully designed accommodations.</p>
    </div>

    @if($useDummy ?? false)
        <div class="demo-banner">
            ⚠️ <strong>Demo Mode:</strong>&nbsp; No active hotels found — showing sample listings.
        </div>
    @endif

    <div class="hotels-grid">
        @foreach($hotels as $hotel)
            @php
                $imgPath = null;
                try {
                    if (is_string($hotel->media ?? null)) {
                        $imgPath = $hotel->media;
                    } elseif (!empty($hotel->media) && is_iterable($hotel->media)) {
                        $first = is_array($hotel->media) ? reset($hotel->media) : (method_exists($hotel->media,'first') ? $hotel->media->first() : null);
                        $imgPath = is_object($first) ? ($first->path ?? null) : ($first['path'] ?? null);
                    }
                } catch (\Throwable $e) { $imgPath = null; }

                $imgSrc = $imgPath
                    ? (str_starts_with($imgPath,'http') ? $imgPath : asset('storage/'.ltrim($imgPath,'/')))
                    : 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop';

                $city = is_array($hotel->address) ? ($hotel->address['city'] ?? '') : (optional($hotel->address)->city ?? '');
                $rating = $hotel->rating ?? 4.0;
                $fullStars = (int)floor($rating);
                $emptyStars = 5 - $fullStars;
            @endphp

            <div class="hotel-card">
                <div class="hotel-img-wrap">
                    <img src="{{ $imgSrc }}" alt="{{ $hotel->name }}" loading="lazy">
                    <span class="hotel-tag">Featured</span>
                    <div class="hotel-rating-badge">
                        <span class="star-icon">★</span>
                        {{ number_format($rating, 1) }}
                    </div>
                </div>
                <div class="hotel-body">
                    <div class="hotel-category">Salon Room</div>
                    <h3 class="hotel-name">{{ $hotel->name }}</h3>
                    @if($city)
                        <div class="hotel-location">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $city }}
                        </div>
                    @endif
                    <p class="hotel-desc">You have the option of canceling by 6pm on the day of arrival. Breakfast must be ordered separately. The max lines is set to three and all over are...</p>
                    <div class="hotel-stars">
                        @for($i=0;$i<$fullStars;$i++)<span class="s">★</span>@endfor
                        @for($i=0;$i<$emptyStars;$i++)<span class="s empty">★</span>@endfor
                    </div>
                    <div class="hotel-footer">
                        <div class="hotel-price">
                            <div class="hotel-price-lbl">Starting from</div>
                            <div class="hotel-price-num">${{ number_format($hotel->base_price, 0) }}<span class="hotel-price-per"> ¬ Night</span></div>
                        </div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── AMENITIES ── --}}
<div style="background:#f1f5f9; padding: 1px 0;" id="amenities">
    <div class="section">
        <div class="section-head">
            <div class="section-kicker">World-Class Amenities</div>
            <h2 class="section-title">Everything <span>You Need</span></h2>
            <p class="section-desc">Indulge in our premium facilities, designed for your comfort and enjoyment.</p>
        </div>
        <div class="amenities-grid">
            <div class="amenity-card">
                <div class="amenity-icon">🏊</div>
                <div class="amenity-name">Infinity Pool</div>
                <div class="amenity-desc">Swim with panoramic views and a serene atmosphere.</div>
            </div>
            <div class="amenity-card">
                <div class="amenity-icon">💆</div>
                <div class="amenity-name">Luxury Spa</div>
                <div class="amenity-desc">Rejuvenate your senses with our world-class spa treatments.</div>
            </div>
            <div class="amenity-card">
                <div class="amenity-icon">🍽️</div>
                <div class="amenity-name">Fine Dining</div>
                <div class="amenity-desc">Experience culinary excellence with our award-winning chefs.</div>
            </div>
            <div class="amenity-card">
                <div class="amenity-icon">💪</div>
                <div class="amenity-name">Fitness Center</div>
                <div class="amenity-desc">Stay fit with our state-of-the-art gym equipment.</div>
            </div>
            <div class="amenity-card">
                <div class="amenity-icon">🚗</div>
                <div class="amenity-name">Valet Parking</div>
                <div class="amenity-desc">Enjoy hassle-free arrival with our professional valet service.</div>
            </div>
            <div class="amenity-card">
                <div class="amenity-icon">📶</div>
                <div class="amenity-name">WiFi & TV (Cable)</div>
                <div class="amenity-desc">High-speed internet and premium cable in every room.</div>
            </div>
        </div>
    </div>
</div>

@endsection
