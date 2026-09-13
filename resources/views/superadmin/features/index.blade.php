@extends('layouts.admin')

@section('header_title', 'Feature Entitlements Catalog')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Header Banner -->
    <div style="background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #047857 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold">CAPABILITIES & ENTITLEMENTS</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">Feature Flags & System Modules</h1>
                <p style="color: #a7f3d0; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">
                    Manage individual feature flags that gate UI elements, operations, and merchant permissions across different industry themes.
                </p>
            </div>
            <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2" style="border-radius: 10px; font-weight: 700;" data-bs-toggle="modal" data-bs-target="#createFeatureModal">
                <i class="fas fa-plus-circle me-1"></i> Add New Feature
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
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

    <!-- Feature Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff;">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark">Registered Entitlements ({{ $features->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Feature Name</th>
                        <th>Feature Key (Code)</th>
                        <th>Target Vertical</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($features as $feature)
                        <tr>
                            <td class="ps-4">
                                <div class="font-weight-bold text-dark">{{ $feature->name }}</div>
                            </td>
                            <td>
                                <code>{{ $feature->key }}</code>
                            </td>
                            <td>
                                @if($feature->theme)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="{{ $feature->theme->icon ?? 'fas fa-tag' }} me-1"></i> {{ $feature->theme->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        Universal (Global)
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small" style="max-width: 320px;">
                                {{ $feature->description ?? 'No description provided.' }}
                            </td>
                            <td>
                                <span class="badge {{ $feature->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($feature->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-light border me-1" data-bs-toggle="modal" data-bs-target="#editFeatureModal_{{ $feature->id }}">
                                    <i class="fas fa-edit text-primary"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('superadmin.features.destroy', $feature->id) }}" onsubmit="return confirm('Are you sure you want to delete this feature flag?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No feature entitlements registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modals Rendered Outside Table -->
@foreach($features as $feature)
    <div class="modal fade" id="editFeatureModal_{{ $feature->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('superadmin.features.update', $feature->id) }}" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Edit Feature: {{ $feature->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Feature Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $feature->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Feature Key (Code)</label>
                        <input type="text" name="key" class="form-control" value="{{ $feature->key }}" required>
                        <small class="text-muted">Unique key used in <code>&#64;feature('key')</code> directives.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Industry Vertical</label>
                        <select name="theme_id" class="form-select">
                            <option value="">Universal / All Verticals</option>
                            @foreach($themes as $thm)
                                <option value="{{ $thm->id }}" {{ $feature->theme_id == $thm->id ? 'selected' : '' }}>{{ $thm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ $feature->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $feature->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $feature->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<!-- Create Feature Modal -->
<div class="modal fade" id="createFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('superadmin.features.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Add Feature Entitlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Feature Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Table Reservation, Kitchen Display, Student Portal" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Feature Key (Code)</label>
                    <input type="text" name="key" class="form-control" placeholder="e.g. table_reservation, kds, hostel_allotment" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Industry Vertical</label>
                    <select name="theme_id" class="form-select">
                        <option value="">Universal / All Verticals</option>
                        @foreach($themes as $thm)
                            <option value="{{ $thm->id }}">{{ $thm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Describe functionality controlled by this flag"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Create Feature</button>
            </div>
        </form>
    </div>
</div>
@endsection
