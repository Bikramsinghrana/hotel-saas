@extends('themes.hotel.layouts.app')

@section('title', 'About Us | ' . ($tenant->name ?? 'Luxury Hotel & Resort'))

@section('content')
<!-- Hero Section -->
<section class="position-relative py-5 bg-dark text-white text-center" style="background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1600&auto=format&fit=crop') center/cover no-repeat; min-height: 360px; display: flex; align-items: center;">
    <div class="container py-4">
        <span class="badge bg-emerald-light text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold text-uppercase letter-spacing-1 mb-3">
            <i class="fas fa-crown me-1"></i> Our Heritage & Story
        </span>
        <h1 class="display-4 fw-bold font-serif mb-3">About {{ $tenant->name ?? 'Our Grand Hotel & Resort' }}</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 700px;">
            Crafting unforgettable stays with world-class hospitality, timeless architecture, and tailored luxury experiences since inception.
        </p>
        <div class="mt-4">
            <a href="{{ url('/') }}" class="text-white-50 text-decoration-none me-2">Home</a>
            <span class="text-white-50">/</span>
            <span class="text-white fw-semibold ms-2">About Us</span>
        </div>
    </div>
</section>

<!-- Story & Vision -->
<section class="py-5 bg-white">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&auto=format&fit=crop" alt="Hotel Interior" class="img-fluid rounded-4 shadow-lg w-100" style="min-height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white rounded-3 shadow-lg border border-light d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="fas fa-award fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">5-Star Luxury Certified</h6>
                            <small class="text-muted">Ranked Top 1% Worldwide</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="text-success fw-bold text-uppercase tracking-wider small"><i class="fas fa-gem me-1"></i> Timeless Elegance</span>
                <h2 class="display-6 fw-bold font-serif text-dark mt-2 mb-4">A Sanctuary of Comfort, Style, and Impeccable Service</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Nestled in an idyllic destination, {{ $tenant->name ?? 'our hotel' }} blends modern luxury with historic charm. Every suite is individually curated to provide the pinnacle of privacy, tranquil relaxation, and inspiring panoramic views.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-success fs-4 mt-1"><i class="fas fa-concierge-bell"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">24/7 Butler Service</h6>
                                <p class="small text-muted mb-0">Personalized attention for every reservation need.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-success fs-4 mt-1"><i class="fas fa-utensils"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Michelin-Inspired Dining</h6>
                                <p class="small text-muted mb-0">Locally sourced farm-to-table culinary delights.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-success fs-4 mt-1"><i class="fas fa-spa"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Holistic Wellness & Spa</h6>
                                <p class="small text-muted mb-0">Rejuvenating thermal baths and organic therapies.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-success fs-4 mt-1"><i class="fas fa-shield-alt"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Safe & Private</h6>
                                <p class="small text-muted mb-0">Discreet VIP hospitality with full privacy protocols.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('rooms.index') }}" class="btn btn-success px-4 py-3 rounded-pill fw-semibold shadow-sm">
                    <i class="fas fa-bed me-2"></i> Explore Accommodations
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Numerical Highlights -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container py-3">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <h2 class="display-5 fw-bold text-success font-serif mb-1">150+</h2>
                <p class="text-muted fw-semibold mb-0">Luxury Rooms & Suites</p>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-5 fw-bold text-success font-serif mb-1">99.4%</h2>
                <p class="text-muted fw-semibold mb-0">Guest Satisfaction</p>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-5 fw-bold text-success font-serif mb-1">18+</h2>
                <p class="text-muted fw-semibold mb-0">Global Hospitality Awards</p>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-5 fw-bold text-success font-serif mb-1">24/7</h2>
                <p class="text-muted fw-semibold mb-0">Dedicated Concierge</p>
            </div>
        </div>
    </div>
</section>

<!-- Amenities Grid -->
<section class="py-5 bg-white">
    <div class="container py-lg-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-success fw-bold text-uppercase tracking-wider small">World-Class Facilities</span>
            <h2 class="display-6 fw-bold font-serif text-dark mt-2">Designed for Unrivaled Comfort</h2>
            <p class="text-muted">Immerse yourself in our premier resort amenities carefully curated for leisure, wellness, and business travelers.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-swimming-pool fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Infinity Edge Pool</h5>
                    <p class="text-muted small mb-0">Temperature-controlled rooftop pool with sunset views and poolside cocktail service.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-hot-tub fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Aura Spa & Sauna</h5>
                    <p class="text-muted small mb-0">Signature Ayurvedic massages, aromatherapy treatments, and serene vitality pools.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-wine-glass-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Cellar & Rooftop Lounge</h5>
                    <p class="text-muted small mb-0">A curated collection of vintage reserves paired with gourmet tapas and live acoustic sets.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-dumbbell fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">State-of-the-Art Fitness</h5>
                    <p class="text-muted small mb-0">Equipped with premium cardio stations, free weights, and private yoga instructors.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-shuttle-van fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Airport Chauffeur Transfer</h5>
                    <p class="text-muted small mb-0">Complimentary luxury sedan transfers with luggage handling direct to your terminal.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 transition-hover text-center">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-wifi fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Ultra-Fast Fiber WiFi</h5>
                    <p class="text-muted small mb-0">High-speed connectivity throughout the property for uninterrupted work and leisure.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1600&auto=format&fit=crop') center/cover;">
    <div class="container py-4">
        <h2 class="display-6 fw-bold font-serif mb-3">Ready to Experience Unmatched Hospitality?</h2>
        <p class="lead text-light opacity-90 mx-auto mb-4" style="max-width: 600px;">
            Book your room today or reach out to our concierge for bespoke requests and custom stay packages.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('rooms.index') }}" class="btn btn-success btn-lg px-4 py-3 rounded-pill fw-semibold shadow">
                <i class="fas fa-calendar-check me-2"></i> Book Your Stay
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-semibold">
                <i class="fas fa-envelope me-2"></i> Contact Concierge
            </a>
        </div>
    </div>
</section>
@endsection
