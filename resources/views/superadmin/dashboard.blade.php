@extends('layouts.admin')

@section('header_title', 'Super Admin Platform Studio')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Header Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold" style="letter-spacing: 0.05em;">SUPER ADMIN MASTER CONTROL</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">Management SaaS Central Control</h1>
                <p style="color: #94a3b8; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">Manage platform verticals, themes, subscription plan entitlements, and merchant accounts.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('superadmin.themes.index') }}" class="btn btn-primary" style="font-weight: 600; border-radius: 10px;">
                    <i class="fas fa-palette me-1"></i> Themes & Verticals
                </a>
                <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-light" style="font-weight: 600; border-radius: 10px;">
                    <i class="fas fa-cubes me-1"></i> Subscription Plans
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Total Tenants</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #1e3a8a;">{{ $stats['total_tenants'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Active Subscriptions</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #059669;">{{ $stats['active_subscriptions'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Main Themes</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #7c3aed;">{{ $stats['total_themes'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Sub-Themes</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #d97706;">{{ $stats['total_sub_themes'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Feature Flags</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #0284c7;">{{ $stats['total_features'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff;">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small text-uppercase font-weight-bold mb-1">Subscription Plans</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #e11d48;">{{ $stats['total_plans'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tenants & Merchants Overview -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-store me-2 text-primary"></i> Registered Merchant Tenants</h5>
            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-outline-primary">View All Merchants</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Business Name</th>
                        <th>Domain</th>
                        <th>Theme / Vertical</th>
                        <th>Sub-Theme Layout</th>
                        <th>Active Plan</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTenants as $t)
                        <tr>
                            <td><span class="badge bg-light text-dark">#{{ $t->id }}</span></td>
                            <td class="font-weight-bold text-dark">{{ $t->name }}</td>
                            <td><code>{{ $t->domain }}</code></td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $t->theme?->name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $t->subTheme?->name ?? 'Default' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    {{ $t->activeSubscription?->plan?->name ?? 'Trialing / None' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('superadmin.tenants.show', $t->id) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-sliders-h me-1"></i> Overrides
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No merchants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
