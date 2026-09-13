@extends('layouts.admin')

@section('header_title', 'Tenant Access & Overrides')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Tenant Header -->
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-outline-light mb-3" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Tenants Directory
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark font-weight-bold">TENANT #{{ $tenant->id }}</span>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">{{ $tenant->name }}</h1>
                </div>
                <p style="color: #c7d2fe; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">
                    <strong>Subdomain:</strong> {{ $tenant->subdomain ?? 'None' }} &bull; 
                    <strong>Active Vertical:</strong> {{ $tenant->theme ? $tenant->theme->name : 'Unassigned' }} &bull;
                    <strong>Active Layout:</strong> {{ $tenant->subTheme ? $tenant->subTheme->name : 'Unassigned' }}
                </p>
            </div>
            <div>
                <div class="bg-white text-dark p-3 rounded shadow-sm text-center" style="min-width: 200px;">
                    <div class="text-muted small text-uppercase font-weight-bold">Active Subscription</div>
                    <div class="font-weight-bold text-primary" style="font-size: 1.2rem;">
                        {{ $tenant->activeSubscription && $tenant->activeSubscription->plan ? $tenant->activeSubscription->plan->name : 'No Active Plan' }}
                    </div>
                    @if($tenant->activeSubscription)
                        <div class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                            Status: {{ ucfirst($tenant->activeSubscription->status) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Feature Overrides Matrix -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden; background: #fff;">
                <div class="card-header bg-white p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-toggle-on text-success me-2"></i> Feature Flag Overrides</h5>
                            <p class="text-muted small mb-0">Force enable or disable individual features specifically for this merchant.</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Feature</th>
                                <th>In Plan?</th>
                                <th>Current Status</th>
                                <th class="text-end pe-4">Override Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $planFeatureIds = ($tenant->activeSubscription && $tenant->activeSubscription->plan) 
                                    ? $tenant->activeSubscription->plan->features->pluck('id')->toArray() 
                                    : [];
                                $overrides = $tenant->featureOverrides->keyBy('feature_id');
                            @endphp
                            @forelse($features as $feat)
                                @php
                                    $inPlan = in_array($feat->id, $planFeatureIds);
                                    $hasOverride = $overrides->has($feat->id);
                                    $overrideVal = $hasOverride ? (bool)$overrides[$feat->id]->is_enabled : null;
                                    $effectiveState = $hasOverride ? $overrideVal : $inPlan;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="font-weight-bold text-dark">{{ $feat->name }}</div>
                                        <code style="font-size:0.75rem;">{{ $feat->key }}</code>
                                    </td>
                                    <td>
                                        @if($inPlan)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Yes</span>
                                        @else
                                            <span class="badge bg-light text-muted border">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($effectiveState)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> Disabled</span>
                                        @endif
                                        @if($hasOverride)
                                            <span class="badge bg-warning text-dark font-weight-bold" style="font-size: 0.65rem;">Override</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <form method="POST" action="{{ route('superadmin.tenants.feature-override', $tenant->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="feature_id" value="{{ $feat->id }}">
                                            @if($effectiveState)
                                                <input type="hidden" name="is_enabled" value="0">
                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Force Disable Feature">
                                                    Disable
                                                </button>
                                            @else
                                                <input type="hidden" name="is_enabled" value="1">
                                                <button type="submit" class="btn btn-xs btn-outline-success" title="Force Enable Feature">
                                                    Enable
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No features configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sub-Theme Access Overrides Matrix -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden; background: #fff;">
                <div class="card-header bg-white p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-brush text-primary me-2"></i> Sub-Theme / Layout Access</h5>
                            <p class="text-muted small mb-0">Permit or restrict access to specific layout styles and front-ends.</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Layout Name</th>
                                <th>Vertical</th>
                                <th>Access</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $planSubThemeIds = ($tenant->activeSubscription && $tenant->activeSubscription->plan) 
                                    ? $tenant->activeSubscription->plan->subThemes->pluck('id')->toArray() 
                                    : [];
                                $subOverrides = $tenant->subThemeAccesses->keyBy('sub_theme_id');
                            @endphp
                            @forelse($subThemes as $st)
                                @php
                                    $inPlanSub = in_array($st->id, $planSubThemeIds);
                                    $hasSubOverride = $subOverrides->has($st->id);
                                    $subOverrideVal = $hasSubOverride ? (bool)$subOverrides[$st->id]->is_allowed : null;
                                    // Default access: inPlan or if plan has no restricted subThemes and st matches tenant's theme
                                    $effectiveSub = $hasSubOverride ? $subOverrideVal : ($inPlanSub || (empty($planSubThemeIds) && (!$st->is_premium || ($tenant->activeSubscription && $tenant->activeSubscription->plan))));
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="font-weight-bold text-dark">{{ $st->name }}</div>
                                        <small class="text-muted"><code>{{ $st->key }}</code></small>
                                        @if($st->is_premium)
                                            <span class="badge bg-warning text-dark font-weight-bold" style="font-size:0.65rem;">Pro</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $st->theme ? $st->theme->name : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($effectiveSub)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Allowed</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i> Locked</span>
                                        @endif
                                        @if($hasSubOverride)
                                            <span class="badge bg-warning text-dark font-weight-bold" style="font-size: 0.65rem;">Override</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <form method="POST" action="{{ route('superadmin.tenants.subtheme-override', $tenant->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="sub_theme_id" value="{{ $st->id }}">
                                            @if($effectiveSub)
                                                <input type="hidden" name="is_allowed" value="0">
                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Revoke Access">
                                                    Revoke
                                                </button>
                                            @else
                                                <input type="hidden" name="is_allowed" value="1">
                                                <button type="submit" class="btn btn-xs btn-outline-success" title="Grant Access">
                                                    Grant
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No sub-theme layouts configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
