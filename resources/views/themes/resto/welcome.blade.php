@extends('themes.resto.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Le Gourmet') . ' | Artisan Dining & Culinary Experience')

@push('styles')
<style>
/* ── RESTAURANT HERO ── */
.resto-hero {
    position: relative;
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, #09090b 0%, #1c1917 50%, #292524 100%);
    color: #fff;
    text-align: center;
}
.resto-hero-bg {
    position: absolute; inset: 0;
    background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&h=900&fit=crop&q=85');
    background-size: cover; background-position: center;
    opacity: 0.32;
}
.resto-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(9,9,11,0.5) 0%, rgba(9,9,11,0.85) 100%);
}
.resto-hero-content {
    position: relative; z-index: 2;
    padding: 3rem 1.5rem 6rem;
    max-width: 860px;
}
.resto-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(245, 158, 11, 0.18); border: 1px solid rgba(245, 158, 11, 0.5);
    color: #fbbf24; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; padding: 0.4rem 1.2rem; border-radius: 99px;
    margin-bottom: 1.5rem;
}
.resto-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 6vw, 4.4rem);
    font-weight: 800; color: #fff; line-height: 1.15;
    margin-bottom: 1.25rem;
}
.resto-title span { color: #f59e0b; }
.resto-subtitle {
    font-size: 1.15rem; color: #d6d3d1; max-width: 580px;
    margin: 0 auto 2.5rem; line-height: 1.7;
}

/* ── QUICK ACTION STRIP ── */
.resto-strip {
    position: relative; z-index: 10;
    max-width: 1000px; margin: -2.5rem auto 0;
    padding: 0 1.5rem;
}
.resto-strip-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    padding: 1.5rem 2rem;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 1rem; border: 1px solid #e7e5e4;
}

/* ── DISH CARDS ── */
.menu-section { padding: 5rem 1.5rem 3rem; }
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.75rem;
}
.resto-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e7e5e4; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    transition: transform 0.25s, box-shadow 0.25s;
    display: flex; flex-direction: column;
}
.resto-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 30px rgba(0,0,0,0.08);
}
.resto-card-img {
    height: 220px; width: 100%; object-fit: cover;
}

/* ── RESERVATION BOX ── */
.reservation-wrap {
    background: #1c1917; color: #fff;
    border-radius: 20px; padding: 3rem 2rem;
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}
</style>
@endpush

@section('content')

<!-- RESTAURANT HERO SECTION -->
<section class="resto-hero">
    <div class="resto-hero-bg"></div>
    <div class="resto-hero-overlay"></div>
    <div class="resto-hero-content">
        <div class="resto-badge">
            <i class="fas fa-utensils"></i> {{ $tenant ? $tenant->name : 'Artisan Kitchen & Bar' }}
        </div>
        <h1 class="resto-title">
            Artisanal Dining & <span>Gourmet Flavours</span>
        </h1>
        <p class="resto-subtitle">
            From Michelin-inspired degustation tasting menus to quick artisan comfort bites, experience the passion of authentic culinary art.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#table-reservation" class="btn btn-warning text-dark font-weight-bold px-4 py-3" style="border-radius: 10px; font-size: 1rem;">
                <i class="fas fa-calendar-alt me-2"></i> Book a Table
            </a>
            <a href="#featured-menu" class="btn btn-outline-light px-4 py-3" style="border-radius: 10px; font-size: 1rem;">
                <i class="fas fa-book-open me-2"></i> View Full Menu
            </a>
        </div>
    </div>
</section>

<!-- QUICK ACTION STRIP -->
<div class="resto-strip">
    <div class="resto-strip-card">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 48px; height: 48px; background: #fef3c7; color: #d97706; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="font-weight-bold text-dark">Open Today: 12:00 PM - 11:30 PM</div>
                <small class="text-muted">Lunch Service, High-Tea & Late Evening Dinner</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="#table-reservation" class="btn btn-dark px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                Reserve Online
            </a>
        </div>
    </div>
</div>

<!-- FEATURED DISHES MENU -->
<section id="featured-menu" class="menu-section">
    <div class="container" style="max-width: 1280px;">
        <div class="text-center mb-5">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 font-weight-bold text-uppercase">Chef's Selection</span>
            <h2 class="font-weight-bold mt-2" style="font-family: 'Playfair Display', serif; font-size: 2.4rem;">Signature Food & Drinks</h2>
            <p class="text-muted mx-auto" style="max-width: 540px;">Crafted with farm-fresh organic ingredients and time-honored recipes.</p>
        </div>

        <div class="menu-grid">
            <!-- Dish 1 -->
            <div class="resto-card">
                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=800&h=600&fit=crop" class="resto-card-img" alt="Truffle Pasta">
                <div class="p-4 d-flex flex-column" style="flex: 1;">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h4 class="font-weight-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Truffle Tagliolini</h4>
                        <span class="font-weight-bold text-danger" style="font-size: 1.25rem;">₹799</span>
                    </div>
                    <p class="text-muted small mb-3">Fresh hand-rolled ribbon pasta with cultured truffle butter and 24-month aged parmesan.</p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="fas fa-leaf text-success me-1"></i> Vegetarian</span>
                        <button class="btn btn-sm btn-outline-danger font-weight-bold" onclick="alert('Added to table order!')">+ Add to Order</button>
                    </div>
                </div>
            </div>

            <!-- Dish 2 -->
            <div class="resto-card">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop" class="resto-card-img" alt="Grilled Steak">
                <div class="p-4 d-flex flex-column" style="flex: 1;">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h4 class="font-weight-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Smoked Prime Tenderloin</h4>
                        <span class="font-weight-bold text-danger" style="font-size: 1.25rem;">₹1,450</span>
                    </div>
                    <p class="text-muted small mb-3">Charcoal seared prime cut served with garlic potato puree, grilled asparagus & pepper jus.</p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-warning text-dark font-weight-bold"><i class="fas fa-crown me-1"></i> Chef's Signature</span>
                        <button class="btn btn-sm btn-outline-danger font-weight-bold" onclick="alert('Added to table order!')">+ Add to Order</button>
                    </div>
                </div>
            </div>

            <!-- Dish 3 -->
            <div class="resto-card">
                <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&h=600&fit=crop" class="resto-card-img" alt="Wood-fired Pizza">
                <div class="p-4 d-flex flex-column" style="flex: 1;">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h4 class="font-weight-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Artisan Burrata Pizza</h4>
                        <span class="font-weight-bold text-danger" style="font-size: 1.25rem;">₹649</span>
                    </div>
                    <p class="text-muted small mb-3">San Marzano tomatoes, fresh creamy burrata, basil oil, and wood-fired blistered crust.</p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border">Wood Fired</span>
                        <button class="btn btn-sm btn-outline-danger font-weight-bold" onclick="alert('Added to table order!')">+ Add to Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TABLE RESERVATION SECTION -->
<section id="table-reservation" class="py-5 bg-light">
    <div class="container" style="max-width: 900px;">
        <div class="reservation-wrap">
            <div class="text-center mb-4">
                <span class="badge bg-warning text-dark font-weight-bold text-uppercase mb-2">Instant Confirmation</span>
                <h2 class="font-weight-bold text-white" style="font-family: 'Playfair Display', serif; font-size: 2.2rem;">Reserve Your Dining Table</h2>
                <p class="text-light small">We look forward to hosting you for an unforgettable gastronomic journey.</p>
            </div>
            <form onsubmit="event.preventDefault(); alert('Reservation submitted! We will confirm via SMS/WhatsApp.');" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Your Name</label>
                    <input type="text" class="form-control" placeholder="e.g. Vikramaditya Singh" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Phone Number</label>
                    <input type="tel" class="form-control" placeholder="+91 98765 43210" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Date</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Dining Slot</label>
                    <select class="form-select">
                        <option>1:00 PM (Lunch)</option>
                        <option>7:30 PM (Dinner)</option>
                        <option selected>8:30 PM (Prime Dinner)</option>
                        <option>10:00 PM (Late Dining)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-light small text-uppercase font-weight-bold">Party Size</label>
                    <select class="form-select">
                        <option>2 Persons (Couple Table)</option>
                        <option selected>4 Persons (Standard Table)</option>
                        <option>6-10 Persons (Family Dining)</option>
                        <option>12+ Persons (Private Dining Hall)</option>
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

<!-- RESTAURANT FEATURES -->
<section class="py-5">
    <div class="container" style="max-width: 1100px;">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-3">
                    <i class="fas fa-seedling text-success fa-2x mb-3"></i>
                    <h5 class="font-weight-bold">Farm to Table</h5>
                    <p class="text-muted small">Daily harvested organic local farm produce.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <i class="fas fa-wine-bottle text-danger fa-2x mb-3"></i>
                    <h5 class="font-weight-bold">Sommelier Cellar</h5>
                    <p class="text-muted small">Hand-selected international wine pairings.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <i class="fas fa-fire text-warning fa-2x mb-3"></i>
                    <h5 class="font-weight-bold">Wood-Fired Oven</h5>
                    <p class="text-muted small">Slow roasted pizzas and artisanal breads.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <i class="fas fa-glass-cheers text-primary fa-2x mb-3"></i>
                    <h5 class="font-weight-bold">Private Dining</h5>
                    <p class="text-muted small">Exclusive celebration spaces for groups.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
