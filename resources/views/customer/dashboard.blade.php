@extends('layouts.app')

@push('styles')
<style>
    .customer-layout {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .customer-layout {
            grid-template-columns: 1fr;
        }
    }

    .customer-sidebar {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 1.5rem 0;
        height: max-content;
    }

    .customer-sidebar-header {
        padding: 0 1.5rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1rem;
    }

    .customer-sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .customer-sidebar-email {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    .customer-nav {
        list-style: none;
    }

    .customer-nav a {
        display: block;
        padding: 0.75rem 1.5rem;
        color: #334155;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .customer-nav a:hover {
        background: #f8fafc;
        color: var(--primary);
    }

    .customer-nav a.active {
        background: var(--primary-light);
        color: var(--primary-dark);
        border-left-color: var(--primary);
    }

    .customer-content-area {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 2rem;
    }

    .dashboard-greeting {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="customer-layout">
    
    <!-- Sidebar -->
    <aside class="customer-sidebar">
        <div class="customer-sidebar-header">
            <div class="customer-sidebar-title">{{ Auth::user()->name ?? 'Customer' }}</div>
            <div class="customer-sidebar-email">{{ Auth::user()->email ?? '' }}</div>
        </div>
        <ul class="customer-nav">
            <li><a href="{{ route('customer.dashboard') }}" class="active">My Dashboard</a></li>
            <li><a href="#">My Bookings</a></li>
            <li><a href="#">Profile Settings</a></li>
            <li><a href="#">Support</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 0.75rem 1.5rem; color: #ef4444; font-weight: 500; font-size: 0.95rem; cursor: pointer; border-left: 3px solid transparent;">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="customer-content-area">
        <h1 class="dashboard-greeting">Welcome back, {{ explode(' ', Auth::user()->name)[0] ?? 'Guest' }}</h1>
        <p class="dashboard-subtitle">Manage your upcoming stays and review your booking history.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Upcoming Stays</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Past Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Saved Hotels</div>
            </div>
        </div>

        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">Recent Activity</h3>
        <div style="padding: 2rem; text-align: center; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: #64748b;">
            No recent bookings found. Start planning your next trip!
            <div style="margin-top: 1rem;">
                <a href="{{ url('/') }}" style="display: inline-block; padding: 0.5rem 1.5rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: 6px; font-weight: 500;">Browse Hotels</a>
            </div>
        </div>
    </div>

</div>
@endsection
