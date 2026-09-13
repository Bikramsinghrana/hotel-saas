@extends('themes.resto.layouts.app')

@section('title', ($tenant ? $tenant->name : 'Burger & Crunch') . ' | Fast Food & Quick Service')

@push('styles')
<style>
    .hero-fast-food {
        background: linear-gradient(135deg, #b91c1c 0%, #ea580c 60%, #f59e0b 100%);
        color: #fff;
        padding: 5rem 1.5rem 6rem;
        text-align: center;
    }
    .fast-card {
        border-radius: 16px;
        overflow: hidden;
        border: 2px solid #fee2e2;
        background: #fff;
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.08);
        transition: transform 0.2s;
    }
    .fast-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@section('content')
<section class="hero-fast-food">
    <div class="container" style="max-width: 850px;">
        <span class="badge bg-warning text-dark font-weight-bold px-3 py-1 mb-2 text-uppercase">
            <i class="fas fa-fire me-1"></i> Hot & Fresh in 15 Mins &bull; Fast Food Sub-Theme
        </span>
        <h1 class="display-3 font-weight-bold text-white mb-3">
            Sizzle, Crunch & Crave!
        </h1>
        <p class="lead text-light mb-4" style="font-size: 1.25rem;">
            Juicy smash burgers, crispy chicken buckets, loaded cheesy fries, and thick shakes delivered lightning fast!
        </p>
        <a href="#quick-menu" class="btn btn-warning text-dark font-weight-bold px-5 py-3" style="font-size: 1.1rem; border-radius: 12px;">
            <i class="fas fa-motorcycle me-2"></i> Order Online Now
        </a>
    </div>
</section>

<div id="quick-menu" class="container py-5">
    <div class="text-center mb-5">
        <h2 class="font-weight-bold text-danger">Top Fan Favorites</h2>
        <p class="text-muted">Order your comfort food favorites with rapid takeout & delivery</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="fast-card h-100 p-3 text-center">
                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&h=450&fit=crop" class="img-fluid rounded mb-3" alt="Double Smash Burger" style="height: 200px; width: 100%; object-fit: cover;">
                <h4 class="font-weight-bold text-dark mb-1">Double Smash Burger</h4>
                <p class="text-muted small">Two 100% prime patties, melted cheddar, caramelized onions & secret sauce.</p>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="font-weight-bold text-danger" style="font-size: 1.3rem;">₹299</span>
                    <button class="btn btn-danger font-weight-bold px-3" onclick="alert('Item added to quick cart!')">+ Add</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fast-card h-100 p-3 text-center">
                <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&h=450&fit=crop" class="img-fluid rounded mb-3" alt="Pepperoni Pizza" style="height: 200px; width: 100%; object-fit: cover;">
                <h4 class="font-weight-bold text-dark mb-1">Cheesy Firecracker Pizza</h4>
                <p class="text-muted small">Hand-tossed crust, spicy arrabbiata sauce, mozzarella blend & jalapenos.</p>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="font-weight-bold text-danger" style="font-size: 1.3rem;">₹399</span>
                    <button class="btn btn-danger font-weight-bold px-3" onclick="alert('Item added to quick cart!')">+ Add</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fast-card h-100 p-3 text-center">
                <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&h=450&fit=crop" class="img-fluid rounded mb-3" alt="Fried Chicken Bucket" style="height: 200px; width: 100%; object-fit: cover;">
                <h4 class="font-weight-bold text-dark mb-1">Crispy Golden Tenders</h4>
                <p class="text-muted small">6pc hand-battered tender loins with garlic peri-peri dip & crinkle fries.</p>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="font-weight-bold text-danger" style="font-size: 1.3rem;">₹349</span>
                    <button class="btn btn-danger font-weight-bold px-3" onclick="alert('Item added to quick cart!')">+ Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
