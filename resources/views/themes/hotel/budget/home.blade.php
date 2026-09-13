@extends('themes.hotel.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Smart Stay') . ' | Budget & Express Hotel')

@push('styles')
<style>
    .hero-budget {
        background: linear-gradient(135deg, #047857 0%, #065f46 50%, #0f172a 100%);
        color: #fff;
        padding: 5rem 1.5rem 6rem;
        text-align: center;
        position: relative;
    }
    .budget-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: transform 0.2s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .budget-card:hover {
        transform: translateY(-4px);
    }
    .price-pill {
        background: #ecfdf5;
        color: #047857;
        font-weight: 800;
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<section class="hero-budget">
    <div class="container" style="max-width: 800px;">
        <span class="badge bg-light text-success font-weight-bold px-3 py-1 mb-2 text-uppercase">
            <i class="fas fa-tag me-1"></i> Best Price Guarantee &bull; Budget Sub-Theme
        </span>
        <h1 class="display-4 font-weight-bold text-white mb-3">
            Smart, Clean & Affordable Stays
        </h1>
        <p class="lead text-light mb-4">
            Everything you need for a comfortable stay with high-speed WiFi, AC rooms, and hassle-free 24/7 check-in.
        </p>
        <a href="{{ route('rooms.index') }}" class="btn btn-warning text-dark font-weight-bold px-4 py-2">
            <i class="fas fa-bolt me-1"></i> Instant Booking
        </a>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">
        @forelse($hotels as $hotel)
            <div class="col-md-6 col-lg-4">
                <div class="budget-card h-100 d-flex flex-column">
                    @php
                        $imgSrc = ($hotel->media && $hotel->media->first()) 
                            ? (filter_var($hotel->media->first()->path, FILTER_VALIDATE_URL) ? $hotel->media->first()->path : asset($hotel->media->first()->path))
                            : 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c0?w=800&h=600&fit=crop';
                    @endphp
                    <img src="{{ $imgSrc }}" alt="{{ $hotel->name }}" style="height: 200px; width:100%; object-fit: cover;">
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <h4 class="font-weight-bold text-dark mb-1">{{ $hotel->name }}</h4>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-success me-1"></i> {{ is_array($hotel->address) ? ($hotel->address['city'] ?? 'City Center') : 'Metro Station Nearby' }}</p>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <div class="price-pill">₹{{ number_format($hotel->base_price ?? 1299, 0) }} <span class="small font-weight-normal text-muted">/day</span></div>
                            <a href="{{ route('rooms.index') }}" class="btn btn-success font-weight-bold">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">No rooms available.</div>
        @endforelse
    </div>
</div>
@endsection
