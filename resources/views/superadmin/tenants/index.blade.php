@extends('layouts.admin')

@section('header_title', 'Merchant Tenants Directory')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Header Banner -->
    <div style="background: linear-gradient(135deg, #18181b 0%, #27272a 60%, #3f3f46 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold">MERCHANT ECOSYSTEM</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">Merchant Tenants & Individual Controls</h1>
                <p style="color: #d4d4d8; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">
                    Inspect all onboarded merchant organizations, view active subscriptions, and configure tenant-specific feature overrides.
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Tenants Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff;">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark">Active Merchant Organizations ({{ $tenants->total() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Merchant Name</th>
                        <th>Subdomain / Domain</th>
                        <th>Industry Vertical</th>
                        <th>Current Layout</th>
                        <th>Subscription Plan</th>
                        <th>Overrides</th>
                        <th class="text-end pe-4">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $t->name }}</div>
                                <small class="text-muted">ID: #{{ $t->id }}</small>
                            </td>
                            <td>
                                <div><code class="text-primary">{{ $t->subdomain ? $t->subdomain . '.management-saas.in' : ($t->domain ?? 'Not set') }}</code></div>
                            </td>
                            <td>
                                @if($t->theme)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="{{ $t->theme->icon ?? 'fas fa-tag' }} me-1"></i> {{ $t->theme->name }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        Not Selected
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($t->subTheme)
                                    <span class="badge bg-light text-dark border">
                                        {{ $t->subTheme->name }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">None</span>
                                @endif
                            </td>
                            <td>
                                @if($t->activeSubscription && $t->activeSubscription->plan)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fas fa-crown me-1"></i> {{ $t->activeSubscription->plan->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">No Active Plan</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $overrideCount = $t->featureOverrides()->count() + $t->subThemeAccesses()->count();
                                @endphp
                                @if($overrideCount > 0)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        {{ $overrideCount }} Custom Rule(s)
                                    </span>
                                @else
                                    <span class="text-muted small">Standard</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('superadmin.tenants.show', $t->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-sliders-h me-1"></i> Manage Access
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No merchant tenants registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
