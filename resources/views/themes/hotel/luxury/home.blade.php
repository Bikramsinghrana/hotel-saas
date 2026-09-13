@extends('themes.hotel.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Grand Palace') . ' | Luxury Resort & Suites')

@push('styles')
<style>
    :root {
        --luxury-gold: #d97706;
        --luxury-dark: #0f172a;
        --luxury-navy: #1e3a8a;
    }
    .hero-luxury {
        position: relative;
        min-height: 88vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #090d16 0%, #172554 60%, #1e1b4b 100%);
        overflow: hidden;
        color: #fff;
    }
    .hero-luxury-bg {
        position: absolute;
        inset: 0;
        background-image: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1600&h=900&fit=crop&q=85');
        background-size: cover;
        background-position: center;
        opacity: 0.35;
    }
    .hero-luxury-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 3rem 1.5rem 6rem;
        max-width: 900px;
    }
    .gold-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(217, 119, 6, 0.2);
        border: 1px solid rgba(217, 119, 6, 0.6);
        color: #fbbf24;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        padding: 0.4rem 1.2rem;
        border-radius: 99px;
        margin-bottom: 1.5rem;
    }
    .luxury-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.5rem, 5.5vw, 4.2rem);
        font-weight: 800;
        line-height: 1.15;
        color: #fff;
        margin-bottom: 1.25rem;
    }
    .luxury-title span {
        color: #f59e0b;
    }
    .search-card-luxury {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.25);
        padding: 1.75rem 2rem;
        margin-top: -3.5rem;
        position: relative;
        z-index: 10;
    }
    .luxury-hotel-card {
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .luxury-hotel-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 30px rgba(0,0,0,0.12);
    }
    .luxury-card-img {
        height: 240px;
        width: 100%;
        object-fit: cover;
    }
    .amenity-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
        color: #475569;
    }
</style>
@endpush

@section('content')
<!-- Luxury Hero Section -->
<section class="hero-luxury">
    <div class="hero-luxury-bg"></div>
    <div class="hero-luxury-content">
        <div class="gold-badge">
            <i class="fas fa-crown"></i> {{ $tenant ? $tenant->name : 'Palace Hotel' }} &bull; Luxury Sub-Theme
        </div>
        <h1 class="luxury-title">
            Experience Unmatched <span>Elegance & Opulence</span>
        </h1>
        <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 640px; margin: 0 auto 2rem; line-height: 1.6;">
            Indulge in world-class hospitality, private infinity pools, curated gourmet dining, and bespoke sanctuary suites.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#accommodations" class="btn btn-primary" style="background: #d97706; border-color: #d97706; padding: 0.8rem 2rem; font-weight: 700;">
                <i class="fas fa-bed me-2"></i> Explore Suites
            </a>
            <a href="#offers" class="btn btn-ghost text-white" style="border-color: rgba(255,255,255,0.4); padding: 0.8rem 2rem; font-weight: 600;">
                <i class="fas fa-tag me-2"></i> View Privileges
            </a>
        </div>
    </div>
</section>

<!-- Luxury Quick Search -->
<div class="container" style="max-width: 1100px;">
    <div class="search-card-luxury">
        <form action="{{ route('rooms.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label small text-muted font-weight-bold text-uppercase">Check-In</label>
                <input type="date" name="check_in" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted font-weight-bold text-uppercase">Check-Out</label>
                <input type="date" name="check_out" class="form-control" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted font-weight-bold text-uppercase">Guests & Rooms</label>
                <select name="guests" class="form-select">
                    <option value="1">1 Guest, 1 Suite</option>
                    <option value="2" selected>2 Guests, 1 Suite</option>
                    <option value="4">4 Guests, 2 Suites</option>
                    <option value="villa">Royal Villa (6+ Guests)</option>
                </select>
            </div>
            <div class="col-md-3 pt-md-4">
                <button type="submit" class="btn w-100 text-white font-weight-bold" style="background: #d97706; padding: 0.7rem; border-radius: 8px;">
                    <i class="fas fa-search me-1"></i> Check Availability
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Accommodations Showcase -->
<section id="accommodations" class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 font-weight-bold text-uppercase">Bespoke Living</span>
            <h2 class="font-weight-bold mt-2" style="font-family: 'Playfair Display', serif; font-size: 2.4rem;">Signature Accommodations</h2>
            <p class="text-muted mx-auto" style="max-width: 580px;">Hand-crafted sanctuary suites designed for tranquil comfort and breathtaking views.</p>
        </div>

        <div class="row g-4">
            @forelse($hotels as $hotel)
                <div class="col-md-6 col-lg-4">
                    <div class="luxury-hotel-card">
                        <div class="position-relative">
                            @php
                                $imgSrc = ($hotel->media && $hotel->media->first()) 
                                    ? (filter_var($hotel->media->first()->path, FILTER_VALIDATE_URL) ? $hotel->media->first()->path : asset($hotel->media->first()->path))
                                    : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop';
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $hotel->name }}" class="luxury-card-img">
                            <span class="badge bg-dark position-absolute top-0 end-0 m-3 px-3 py-2 font-weight-bold" style="background: rgba(15,23,42,0.85) !important;">
                                ★ {{ $hotel->rating ?? '4.9' }} Luxury
                            </span>
                        </div>
                        <div class="p-4 d-flex flex-column" style="flex: 1;">
                            <h4 class="font-weight-bold mb-1" style="font-family: 'Playfair Display', serif;">{{ $hotel->name }}</h4>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-map-marker-alt text-warning me-1"></i> {{ is_array($hotel->address) ? ($hotel->address['city'] ?? 'Prime Waterfront') : 'Scenic Location' }}
                            </p>
                            <div class="d-flex gap-1 flex-wrap mb-4">
                                <span class="amenity-badge"><i class="fas fa-water text-primary me-1"></i> Infinity Pool</span>
                                <span class="amenity-badge"><i class="fas fa-spa text-warning me-1"></i> Wellness Spa</span>
                                <span class="amenity-badge"><i class="fas fa-wifi text-success me-1"></i> 1Gbps WiFi</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <div>
                                    <small class="text-muted">Starting from</small>
                                    <div class="font-weight-bold text-dark" style="font-size: 1.3rem;">₹{{ number_format($hotel->base_price ?? 4500, 2) }} <span class="small text-muted font-weight-normal">/night</span></div>
                                </div>
                                <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-primary" style="background: #1e3a8a; border-color: #1e3a8a;">
                                    View Suite &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <p>No accommodations registered for this property yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Luxury Privileges & Offers -->
@if(isset($offers) && $offers->count() > 0)
<section id="offers" class="py-5" style="background: #0f172a; color: #fff;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark font-weight-bold text-uppercase">Exclusive Privileges</span>
            <h2 class="font-weight-bold mt-2 text-white" style="font-family: 'Playfair Display', serif;">Member Packages & Offers</h2>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach($offers as $offer)
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 rounded border border-secondary text-center" style="background: rgba(255,255,255,0.04); border-radius: 16px;">
                        <span class="badge bg-warning text-dark font-weight-bold mb-2">CODE: {{ $offer->code }}</span>
                        <h4 class="text-white font-weight-bold">{{ $offer->title ?? 'Complimentary Resort Credit' }}</h4>
                        <p class="text-muted small">{{ $offer->description ?? 'Enjoy premium dining discounts and free room upgrade.' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
