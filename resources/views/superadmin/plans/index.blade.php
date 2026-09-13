@extends('layouts.admin')

@section('header_title', 'Subscription Plans & Tier Studio')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Header Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold">MONETIZATION & PACKAGES</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">SaaS Subscription Plans & Features</h1>
                <p style="color: #94a3b8; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">
                    Define pricing packages, assign industry verticals, unlock specific layouts, and bind feature entitlements.
                </p>
            </div>
            <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2" style="border-radius: 10px; font-weight: 700;" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i class="fas fa-plus-circle me-1"></i> Create New Plan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="row g-4">
        @forelse($plans as $plan)
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm position-relative" style="border-radius: 16px; background: #fff; overflow: hidden; display: flex; flex-direction: column;">
                    @if($plan->is_featured)
                        <div style="position: absolute; top: 12px; right: -30px; background: #f59e0b; color: #000; font-size: 0.65rem; font-weight: 800; padding: 4px 30px; transform: rotate(45deg); letter-spacing: 0.05em; text-transform: uppercase;">
                            Popular
                        </div>
                    @endif

                    <div class="card-body p-4 d-flex flex-column" style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-primary border font-weight-bold mb-1">
                                    {{ $plan->theme ? $plan->theme->name : 'All Verticals (Universal)' }}
                                </span>
                                <h3 class="font-weight-bold text-dark mb-1" style="font-size: 1.4rem;">{{ $plan->name }}</h3>
                            </div>
                            <span class="badge {{ $plan->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>

                        <p class="text-muted small mb-3" style="min-height: 2.5em;">
                            {{ $plan->description ?? 'Standard merchant tier with curated features.' }}
                        </p>

                        <!-- Price Tag -->
                        <div class="bg-light p-3 rounded mb-3 text-center border">
                            <div class="text-muted small text-uppercase font-weight-bold">Price / Billing</div>
                            <div style="font-size: 2rem; font-weight: 800; color: #1e293b;">
                                ₹{{ number_format($plan->price, 2) }}
                                <span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">/ {{ $plan->billing_period }}</span>
                            </div>
                            @if($plan->trial_days > 0)
                                <div class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">
                                    <i class="fas fa-gift me-1"></i> {{ $plan->trial_days }} Days Free Trial
                                </div>
                            @endif
                        </div>

                        <!-- Sub-themes Allowed -->
                        <div class="mb-3">
                            <div class="text-uppercase text-muted font-weight-bold small mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                <i class="fas fa-layer-group text-primary me-1"></i> Allowed Layouts ({{ $plan->subThemes->count() }})
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($plan->subThemes as $subTheme)
                                    <span class="badge bg-light text-dark border" style="font-size: 0.75rem;">
                                        {{ $subTheme->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small font-italic">No specific sub-theme restrictions</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Features Included -->
                        <div class="mb-4" style="flex: 1;">
                            <div class="text-uppercase text-muted font-weight-bold small mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                <i class="fas fa-check-double text-success me-1"></i> Included Entitlements ({{ $plan->features->count() }})
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($plan->features as $feat)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">
                                        <i class="fas fa-check me-1"></i> {{ $feat->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small font-italic">No features bound to this plan</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Subscribers & Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <div class="small text-muted">
                                <i class="fas fa-users me-1 text-primary"></i> <strong>{{ $plan->subscriptions->where('status', 'active')->count() }}</strong> active subscribers
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPlanModal_{{ $plan->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                @if($plan->subscriptions->where('status', 'active')->count() === 0)
                                    <form method="POST" action="{{ route('superadmin.plans.destroy', $plan->id) }}" onsubmit="return confirm('Delete this subscription plan?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Plan Modal -->
            <div class="modal fade" id="editPlanModal_{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('superadmin.plans.update', $plan->id) }}" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">Edit Plan: {{ $plan->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label font-weight-bold">Plan Title</label>
                                    <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label font-weight-bold">Industry Vertical</label>
                                    <select name="theme_id" class="form-select">
                                        <option value="">Universal / All Verticals</option>
                                        @foreach($themes as $thm)
                                            <option value="{{ $thm->id }}" {{ $plan->theme_id == $thm->id ? 'selected' : '' }}>{{ $thm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Price (₹)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $plan->price }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Billing Period</label>
                                    <select name="billing_period" class="form-select">
                                        <option value="monthly" {{ $plan->billing_period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $plan->billing_period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="lifetime" {{ $plan->billing_period === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Trial Days</label>
                                    <input type="number" name="trial_days" class="form-control" value="{{ $plan->trial_days }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label font-weight-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $plan->description }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label font-weight-bold text-uppercase text-primary small">Select Allowed Layouts / Sub-Themes</label>
                                    <div class="row g-2 p-2 bg-light rounded border" style="max-height: 180px; overflow-y: auto;">
                                        @php $planSubIds = $plan->subThemes->pluck('id')->toArray(); @endphp
                                        @foreach($subThemes as $st)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" name="sub_theme_ids[]" value="{{ $st->id }}" class="form-check-input" id="plan_{{ $plan->id }}_st_{{ $st->id }}" {{ in_array($st->id, $planSubIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="plan_{{ $plan->id }}_st_{{ $st->id }}">
                                                        <strong>{{ $st->theme ? $st->theme->name : 'Theme' }}:</strong> {{ $st->name }}
                                                        @if($st->is_premium) <span class="badge bg-warning text-dark font-weight-bold ms-1" style="font-size:0.65rem;">Pro</span> @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label font-weight-bold text-uppercase text-success small">Select Included Features</label>
                                    <div class="row g-2 p-2 bg-light rounded border" style="max-height: 200px; overflow-y: auto;">
                                        @php $planFeatIds = $plan->features->pluck('id')->toArray(); @endphp
                                        @foreach($features as $ft)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" name="feature_ids[]" value="{{ $ft->id }}" class="form-check-input" id="plan_{{ $plan->id }}_ft_{{ $ft->id }}" {{ in_array($ft->id, $planFeatIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="plan_{{ $plan->id }}_ft_{{ $ft->id }}">
                                                        <strong>{{ $ft->name }}</strong> <code style="font-size:0.75rem;">({{ $ft->key }})</code>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $plan->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-center pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="feat_plan_{{ $plan->id }}" {{ $plan->is_featured ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="feat_plan_{{ $plan->id }}">Highlight as Featured / Popular</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <h4>No Subscription Plans Configured</h4>
                <p>Click "Create New Plan" to design your first subscription tier.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('superadmin.plans.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-layer-group text-primary me-2"></i> Create New Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label font-weight-bold">Plan Title</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Hotel Growth, Restaurant Pro, Enterprise" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label font-weight-bold">Industry Vertical</label>
                        <select name="theme_id" class="form-select">
                            <option value="">Universal / All Verticals</option>
                            @foreach($themes as $thm)
                                <option value="{{ $thm->id }}">{{ $thm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Price (₹)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Billing Period</label>
                        <select name="billing_period" class="form-select">
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Trial Days</label>
                        <input type="number" name="trial_days" class="form-control" value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Summary of benefits and merchant quotas"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-weight-bold text-uppercase text-primary small">Select Allowed Layouts / Sub-Themes</label>
                        <div class="row g-2 p-2 bg-light rounded border" style="max-height: 180px; overflow-y: auto;">
                            @foreach($subThemes as $st)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="sub_theme_ids[]" value="{{ $st->id }}" class="form-check-input" id="new_plan_st_{{ $st->id }}">
                                        <label class="form-check-label small" for="new_plan_st_{{ $st->id }}">
                                            <strong>{{ $st->theme ? $st->theme->name : 'Theme' }}:</strong> {{ $st->name }}
                                            @if($st->is_premium) <span class="badge bg-warning text-dark font-weight-bold ms-1" style="font-size:0.65rem;">Pro</span> @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-weight-bold text-uppercase text-success small">Select Included Features</label>
                        <div class="row g-2 p-2 bg-light rounded border" style="max-height: 200px; overflow-y: auto;">
                            @foreach($features as $ft)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="feature_ids[]" value="{{ $ft->id }}" class="form-check-input" id="new_plan_ft_{{ $ft->id }}">
                                        <label class="form-check-label small" for="new_plan_ft_{{ $ft->id }}">
                                            <strong>{{ $ft->name }}</strong> <code style="font-size:0.75rem;">({{ $ft->key }})</code>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-center pt-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="new_plan_is_featured">
                            <label class="form-check-label font-weight-bold" for="new_plan_is_featured">Highlight as Featured / Popular</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Create Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection
