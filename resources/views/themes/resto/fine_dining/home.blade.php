@extends('themes.resto.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Le Gourmet') . ' | Fine Dining & Culinary Art')

@push('styles')
<style>
    .hero-fine-dining {
        position: relative;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #09090b 0%, #1c1917 50%, #292524 100%);
        color: #fff;
        text-align: center;
        overflow: hidden;
    }
    .hero-fine-bg {
        position: absolute;
        inset: 0;
        background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&h=900&fit=crop&q=85');
        background-size: cover;
        background-position: center;
        opacity: 0.28;
    }
    .dish-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e7e5e4;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
    }
    .dish-card:hover {
        transform: translateY(-5px);
    }
    .dish-img {
        height: 220px;
        width: 100%;
        object-fit: cover;
    }
    .reservation-card {
        background: #1c1917;
        color: #fff;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
</style>
@endpush

@section('content')
<!-- Fine Dining Hero -->
<section class="hero-fine-dining">
    <div class="hero-fine-bg"></div>
    <div class="container position-relative py-5" style="z-index: 2; max-width: 860px;">
        <span class="badge bg-warning text-dark font-weight-bold px-3 py-1 mb-3 text-uppercase" style="letter-spacing: 0.15em;">
            <i class="fas fa-utensils me-1"></i> {{ $tenant ? $tenant->name : 'Urban Bistro' }} &bull; Fine Dining Sub-Theme
        </span>
        <h1 class="display-3 font-weight-bold text-white mb-3" style="font-family: 'Playfair Display', serif;">
            An Unforgettable <em>Culinary Symphony</em>
        </h1>
        <p class="lead text-light mb-4" style="color: #d6d3d1 !important; font-size: 1.2rem;">
            Immerse yourself in authentic gourmet flavours, Michelin-inspired degustation menus, and rare vintage cellar collections.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#table-booking" class="btn btn-warning text-dark font-weight-bold px-4 py-3" style="border-radius: 10px; font-size: 1rem;">
                <i class="fas fa-calendar-check me-2"></i> Reserve a Table
            </a>
            <a href="#tasting-menu" class="btn btn-outline-light px-4 py-3" style="border-radius: 10px; font-size: 1rem;">
                <i class="fas fa-book-open me-2"></i> Chef's Tasting Menu
            </a>
        </div>
    </div>
</section>

<!-- Signature Dishes Menu -->
<section id="tasting-menu" class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 font-weight-bold text-uppercase">Haute Cuisine</span>
            <h2 class="font-weight-bold mt-2" style="font-family: 'Playfair Display', serif; font-size: 2.3rem;">Signature Tasting Courses</h2>
            <p class="text-muted mx-auto" style="max-width: 540px;">Locally sourced organic ingredients paired with artisanal culinary mastery.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="dish-card h-100 d-flex flex-column">
                    <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=800&h=600&fit=crop" class="dish-img" alt="Smoked Duck Breast">
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <h4 class="font-weight-bold mb-0" style="font-family: 'Playfair Display', serif;">Truffle Butter Tagliolini</h4>
                            <span class="font-weight-bold text-danger" style="font-size: 1.2rem;">₹850</span>
                        </div>
                        <p class="text-muted small mb-3">Handmade pasta tossed in aged parmigiano-reggiano and freshly shaved Umbrian black truffles.</p>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border">Chef's Choice</span>
                            <a href="#table-booking" class="btn btn-sm btn-outline-dark">Order / Reserve</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="dish-card h-100 d-flex flex-column">
                    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop" class="dish-img" alt="Wagyu Ribeye">
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <h4 class="font-weight-bold mb-0" style="font-family: 'Playfair Display', serif;">A5 Wagyu Striploin</h4>
                            <span class="font-weight-bold text-danger" style="font-size: 1.2rem;">₹2,400</span>
                        </div>
                        <p class="text-muted small mb-3">Seared Japanese Wagyu served with roasted shallot jus, smoked bone marrow, and wild forest mushrooms.</p>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-warning text-dark font-weight-bold">Signature</span>
                            <a href="#table-booking" class="btn btn-sm btn-outline-dark">Order / Reserve</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="dish-card h-100 d-flex flex-column">
                    <img src="https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=800&h=600&fit=crop" class="dish-img" alt="Wild Berry Souffle">
                    <div class="p-4 d-flex flex-column" style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <h4 class="font-weight-bold mb-0" style="font-family: 'Playfair Display', serif;">Madagascar Vanilla Soufflé</h4>
                            <span class="font-weight-bold text-danger" style="font-size: 1.2rem;">₹650</span>
                        </div>
                        <p class="text-muted small mb-3">Airy baked soufflé infused with Grand Marnier, served with Tahitian vanilla bean anglaise.</p>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border">Dessert</span>
                            <a href="#table-booking" class="btn btn-sm btn-outline-dark">Order / Reserve</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table Reservation Section -->
<section id="table-booking" class="py-5 bg-light">
    <div class="container" style="max-width: 900px;">
        <div class="reservation-card">
            <div class="text-center mb-4">
                <span class="badge bg-warning text-dark font-weight-bold text-uppercase mb-2">Instant Confirmation</span>
                <h2 class="font-weight-bold text-white" style="font-family: 'Playfair Display', serif;">Reserve Your Table</h2>
                <p class="text-light small">Experience an intimate dining evening at {{ $tenant ? $tenant->name : 'our restaurant' }}.</p>
            </div>
            <form onsubmit="event.preventDefault(); alert('Table reservation request received! Our host will confirm via WhatsApp/SMS.');" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Guest Name</label>
                    <input type="text" class="form-control" placeholder="e.g. Rahul Sharma" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Contact Number</label>
                    <input type="tel" class="form-control" placeholder="+91 98765 43210" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Date</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Time Slot</label>
                    <select class="form-select">
                        <option>7:00 PM (Dinner)</option>
                        <option>8:30 PM (Prime Dinner)</option>
                        <option>10:00 PM (Late Night)</option>
                        <option>1:00 PM (Lunch)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Party Size</label>
                    <select class="form-select">
                        <option>2 Guests (Intimate Table)</option>
                        <option>4 Guests (Family Table)</option>
                        <option>6+ Guests (Private Dining Hall)</option>
                    </select>
                </div>
                <div class="col-12 pt-3">
                    <button type="submit" class="btn btn-warning w-100 py-3 font-weight-bold text-dark" style="font-size: 1.1rem; border-radius: 10px;">
                        <i class="fas fa-check-circle me-1"></i> Confirm Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
