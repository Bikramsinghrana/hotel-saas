@extends('layouts.app')

@section('title', config('app.name', 'Management SaaS') . ' | Multi-Vertical Business Operating System')

@push('styles')
<style>
    .portal-hero {
        background: linear-gradient(135deg, #090d16 0%, #1e1b4b 50%, #311042 100%);
        color: #fff;
        padding: 6rem 1.5rem 5rem;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    .portal-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #fbbf24;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 0.4rem 1.2rem;
        border-radius: 99px;
        margin-bottom: 1.5rem;
    }
    .theme-preview-card {
        border-radius: 20px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .theme-preview-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    .theme-card-banner {
        height: 180px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .theme-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
        display: flex;
        align-items: flex-end;
        padding: 1.25rem;
    }
    .subtheme-pill {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        background: #f1f5f9;
        color: #334155;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
    }
    .subtheme-pill:hover {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<!-- SaaS Multi-Vertical Hero -->
<section class="portal-hero">
    <div class="container position-relative" style="max-width: 900px; z-index: 2;">
        <div class="portal-badge">
            <i class="fas fa-layer-group"></i> Multi-Vertical SaaS Architecture &bull; management-saas.in
        </div>
        <h1 class="display-3 font-weight-bold text-white mb-3" style="font-family: 'Playfair Display', serif;">
            One Platform. <span style="background: linear-gradient(to right, #38bdf8, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Infinite Business Verticals.</span>
        </h1>
        <p class="lead text-light mb-4" style="color: #cbd5e1 !important; font-size: 1.2rem; line-height: 1.7;">
            Empower your enterprise with dedicated, tailored operating systems and curated sub-theme designs for Hotels, Restaurants, Schools, Temples, and beyond.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#verticals" class="btn btn-warning text-dark font-weight-bold px-4 py-3" style="border-radius: 12px; font-size: 1rem;">
                <i class="fas fa-th-large me-2"></i> Explore Industry Themes
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light px-4 py-3" style="border-radius: 12px; font-size: 1rem;">
                    <i class="fas fa-tachometer-alt me-2"></i> Open Merchant Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-3" style="border-radius: 12px; font-size: 1rem;">
                    <i class="fas fa-sign-in-alt me-2"></i> Merchant Login
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Industry Verticals Catalog -->
<section id="verticals" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 font-weight-bold text-uppercase">Dedicated Solutions</span>
            <h2 class="font-weight-bold mt-2" style="font-size: 2.3rem;">Explore Live Theme Storefronts</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Each industry vertical provides tailored operational workflows, specialized sidebars, and customizable sub-theme layouts.
            </p>
        </div>

        <div class="row g-4">
            <!-- 1. Hotel & Hospitality Theme -->
            <div class="col-lg-6">
                <div class="theme-preview-card">
                    <div class="theme-card-banner" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&h=500&fit=crop');">
                        <div class="theme-card-overlay">
                            <div>
                                <span class="badge bg-warning text-dark font-weight-bold mb-1">THEME: HOTEL & RESORTS</span>
                                <h3 class="text-white font-weight-bold mb-0">Hospitality Operating System</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <p class="text-muted mb-3" style="line-height: 1.5;">
                            End-to-end room inventory, dynamic rate rules, instant guest bookings, coupons & offers, and housekeeping management.
                        </p>

                        <div class="mb-4">
                            <div class="text-uppercase text-muted font-weight-bold small mb-2" style="font-size: 0.75rem;">
                                <i class="fas fa-brush text-primary me-1"></i> Available Sub-Theme Layouts:
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('hotel.welcome', ['subtheme' => 'luxury']) }}" class="subtheme-pill">
                                    <i class="fas fa-crown text-warning me-1"></i> Luxury Resort
                                </a>
                                <a href="{{ route('hotel.welcome', ['subtheme' => 'budget']) }}" class="subtheme-pill">
                                    <i class="fas fa-tag text-success me-1"></i> Budget Stay
                                </a>
                                <a href="{{ route('hotel.welcome', ['subtheme' => 'boutique']) }}" class="subtheme-pill">
                                    <i class="fas fa-spa text-info me-1"></i> Boutique Heritage
                                </a>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <code class="text-muted small">URL: /hotel</code>
                            <a href="{{ route('hotel.welcome') }}" class="btn btn-primary font-weight-bold px-4">
                                Launch Hotel Demo &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Restaurant & Dining Theme -->
            <div class="col-lg-6">
                <div class="theme-preview-card">
                    <div class="theme-card-banner" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&h=500&fit=crop');">
                        <div class="theme-card-overlay">
                            <div>
                                <span class="badge bg-danger text-white font-weight-bold mb-1">THEME: RESTAURANT & DINING</span>
                                <h3 class="text-white font-weight-bold mb-0">Food Service & POS System</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <p class="text-muted mb-3" style="line-height: 1.5;">
                            Digital menus & categories, QR dine-in ordering, live table reservations, Kitchen Display (KDS), and fast POS cashier billing.
                        </p>

                        <div class="mb-4">
                            <div class="text-uppercase text-muted font-weight-bold small mb-2" style="font-size: 0.75rem;">
                                <i class="fas fa-brush text-danger me-1"></i> Available Sub-Theme Layouts:
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('resto.welcome', ['subtheme' => 'fine_dining']) }}" class="subtheme-pill">
                                    <i class="fas fa-wine-glass-alt text-danger me-1"></i> Fine Dining & Bistro
                                </a>
                                <a href="{{ route('resto.welcome', ['subtheme' => 'fast_food']) }}" class="subtheme-pill">
                                    <i class="fas fa-hamburger text-warning me-1"></i> Fast Food & Combos
                                </a>
                                <a href="{{ route('resto.welcome', ['subtheme' => 'cafe']) }}" class="subtheme-pill">
                                    <i class="fas fa-coffee text-success me-1"></i> Artisan Cafe
                                </a>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <code class="text-muted small">URL: /resto</code>
                            <a href="{{ route('resto.welcome') }}" class="btn btn-danger font-weight-bold px-4">
                                Launch Restaurant Demo &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. School & Campus (Upcoming) -->
            <div class="col-lg-6">
                <div class="theme-preview-card opacity-75">
                    <div class="theme-card-banner" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=900&h=500&fit=crop');">
                        <div class="theme-card-overlay">
                            <div>
                                <span class="badge bg-info text-dark font-weight-bold mb-1">UPCOMING VERTICAL</span>
                                <h3 class="text-white font-weight-bold mb-0">School & Campus ERP</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <p class="text-muted mb-3">
                            Student admissions, attendance, automated fee collection, exam schedules, and parent communication portal.
                        </p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-muted border">In Active Development</span>
                            <button class="btn btn-sm btn-light border" disabled>Coming Soon</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Temple & Trust (Upcoming) -->
            <div class="col-lg-6">
                <div class="theme-preview-card opacity-75">
                    <div class="theme-card-banner" style="background-image: url('https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=900&h=500&fit=crop');">
                        <div class="theme-card-overlay">
                            <div>
                                <span class="badge bg-warning text-dark font-weight-bold mb-1">UPCOMING VERTICAL</span>
                                <h3 class="text-white font-weight-bold mb-0">Temple & Charitable Trust Portal</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <p class="text-muted mb-3">
                            Online Pooja & Darshan booking, 80G tax donation receipts, dynamic festival calendar, and seva volunteer tracking.
                        </p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-muted border">In Active Development</span>
                            <button class="btn btn-sm btn-light border" disabled>Coming Soon</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
