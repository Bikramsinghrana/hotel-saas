@extends('layouts.admin')

@section('header_title', 'Theme & Vertical Studio')

@section('content')
<div class="container-fluid py-2">
    <!-- Super Admin Header Banner -->
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold">THEME & VERTICAL STUDIO</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">Industry Verticals & Layout Engine</h1>
                <p style="color: #cbd5e1; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">
                    Create, configure, and manage primary industry verticals (Hotel, Restaurant, School, Temple) and their sub-theme layouts.
                </p>
            </div>
            <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2" style="border-radius: 10px; font-weight: 700;" data-bs-toggle="modal" data-bs-target="#createThemeModal">
                <i class="fas fa-plus-circle me-1"></i> Add New Vertical
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

    <!-- Industry Verticals List -->
    <div class="row g-4">
        @forelse($themes as $theme)
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff;">
                    <!-- Vertical Header -->
                    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 52px; height: 52px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                                @if($theme->key === 'hotel') 🏨 @elseif($theme->key === 'restaurant') 🍽️ @elseif($theme->key === 'school') 🎓 @elseif($theme->key === 'temple') 🛕 @else 🏢 @endif
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 font-weight-bold text-dark">{{ $theme->name }}</h4>
                                    <span class="badge {{ $theme->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($theme->status) }}
                                    </span>
                                    <span class="badge bg-light text-muted border">key: {{ $theme->key }}</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">{{ $theme->description ?? 'No description provided.' }}</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                <i class="fas fa-store me-1"></i> {{ $theme->tenants->count() }} Merchants
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                <i class="fas fa-layer-group me-1"></i> {{ $theme->subThemes->count() }} Layouts
                            </span>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSubThemeModal_{{ $theme->id }}">
                                <i class="fas fa-plus me-1"></i> Add Layout
                            </button>
                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editThemeModal_{{ $theme->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('superadmin.themes.toggle-status', $theme->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $theme->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Toggle Active Status">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            @if($theme->tenants->count() === 0)
                                <form method="POST" action="{{ route('superadmin.themes.destroy', $theme->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this vertical?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Vertical">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Themes / Layouts Grid -->
                    <div class="card-body p-4" style="background: #f8fafc;">
                        <h6 class="text-uppercase text-muted font-weight-bold small mb-3" style="letter-spacing: 0.05em;">
                            Sub-Themes & Operational Layouts ({{ $theme->subThemes->count() }})
                        </h6>

                        <div class="row g-3">
                            @forelse($theme->subThemes as $subTheme)
                                <div class="col-md-6 col-xl-4">
                                    <div class="card h-100 border shadow-sm" style="border-radius: 12px; transition: all 0.2s;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <div class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $subTheme->name }}</div>
                                                    <div class="text-muted small"><code>{{ $subTheme->key }}</code> &bull; <span class="text-uppercase font-weight-bold" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $subTheme->type) }}</span></div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    @if($subTheme->is_premium)
                                                        <span class="badge bg-warning text-dark font-weight-bold"><i class="fas fa-gem me-1"></i> Pro</span>
                                                    @else
                                                        <span class="badge bg-light text-muted border">Standard</span>
                                                    @endif
                                                    <span class="badge {{ $subTheme->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($subTheme->status) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <p class="text-muted small mb-3" style="line-height: 1.4; min-height: 2.8em;">
                                                {{ $subTheme->description ?? 'No layout description.' }}
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <small class="text-muted">
                                                    <i class="fas fa-store me-1"></i> {{ $subTheme->tenants()->count() }} Active
                                                </small>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn btn-xs btn-light border" data-bs-toggle="modal" data-bs-target="#editSubThemeModal_{{ $subTheme->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form method="POST" action="{{ route('superadmin.sub-themes.toggle-status', $subTheme->id) }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-light border" title="Toggle Active">
                                                            <i class="fas fa-power-off text-muted"></i>
                                                        </button>
                                                    </form>
                                                    @if($subTheme->tenants()->count() === 0)
                                                        <form method="POST" action="{{ route('superadmin.sub-themes.destroy', $subTheme->id) }}" style="display:inline;" onsubmit="return confirm('Delete this layout?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete Layout">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit SubTheme Modal -->
                                <div class="modal fade" id="editSubThemeModal_{{ $subTheme->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('superadmin.sub-themes.update', $subTheme->id) }}" class="modal-content">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title font-weight-bold">Edit Layout: {{ $subTheme->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Layout Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $subTheme->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Operational Type</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="single_hotel" {{ $subTheme->type === 'single_hotel' ? 'selected' : '' }}>Single Property / Resort</option>
                                                        <option value="multi_hotel" {{ $subTheme->type === 'multi_hotel' ? 'selected' : '' }}>Multi-Property / Chain</option>
                                                        <option value="dine_in" {{ $subTheme->type === 'dine_in' ? 'selected' : '' }}>Dine-In Restaurant</option>
                                                        <option value="takeaway" {{ $subTheme->type === 'takeaway' ? 'selected' : '' }}>Quick Service / Takeaway</option>
                                                        <option value="campus" {{ $subTheme->type === 'campus' ? 'selected' : '' }}>School / College Campus</option>
                                                        <option value="trust" {{ $subTheme->type === 'trust' ? 'selected' : '' }}>Temple / Trust Portal</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label font-weight-bold">Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $subTheme->description }}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label font-weight-bold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $subTheme->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $subTheme->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="is_premium" value="1" class="form-check-input" id="prem_{{ $subTheme->id }}" {{ $subTheme->is_premium ? 'checked' : '' }}>
                                                            <label class="form-check-label font-weight-bold" for="prem_{{ $subTheme->id }}">Premium / Pro Only</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary font-weight-bold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-4 bg-white rounded border text-muted">
                                        <i class="fas fa-layer-group fa-2x mb-2 text-muted"></i>
                                        <div>No sub-theme layouts registered for this vertical yet.</div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addSubThemeModal_{{ $theme->id }}">
                                            + Add First Layout
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Vertical Modal -->
            <div class="modal fade" id="editThemeModal_{{ $theme->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('superadmin.themes.update', $theme->id) }}" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">Edit Industry Vertical: {{ $theme->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Vertical Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $theme->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Icon Class (FontAwesome)</label>
                                <input type="text" name="icon" class="form-control" value="{{ $theme->icon }}" placeholder="fa-hotel, fa-utensils, fa-school">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ $theme->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $theme->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $theme->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Update Vertical</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add SubTheme Modal -->
            <div class="modal fade" id="addSubThemeModal_{{ $theme->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('superadmin.themes.sub-themes.store', $theme->id) }}" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">Add New Layout to {{ $theme->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Layout Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Luxury Resort, Fast Food, Modern Campus" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Key (Identifier)</label>
                                <input type="text" name="key" class="form-control" placeholder="e.g. luxury, budget, fine_dining (leave blank for auto-generate)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Operational Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="single_hotel">Single Property / Resort</option>
                                    <option value="multi_hotel">Multi-Property / Chain</option>
                                    <option value="dine_in">Dine-In Restaurant</option>
                                    <option value="takeaway">Quick Service / Takeaway</option>
                                    <option value="campus">School / College Campus</option>
                                    <option value="trust">Temple / Trust Portal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Summary of layout features and target audience"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_premium" value="1" class="form-check-input" id="prem_new_{{ $theme->id }}">
                                        <label class="form-check-label font-weight-bold" for="prem_new_{{ $theme->id }}">Premium / Pro Only</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">+ Create Layout</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <h4>No Industry Verticals Found</h4>
                <p>Click "Add New Vertical" above to register your first SaaS industry theme.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Vertical Modal -->
<div class="modal fade" id="createThemeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('superadmin.themes.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Register New Industry Vertical</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Vertical Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. School Management, Temple Trust, Clinic" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Unique Key (Identifier)</label>
                    <input type="text" name="key" class="form-control" placeholder="e.g. school, temple, clinic (auto-generated if empty)">
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Icon Class (FontAwesome)</label>
                    <input type="text" name="icon" class="form-control" placeholder="fa-school, fa-hospital, fa-om">
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of vertical capabilities"></textarea>
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
                <button type="submit" class="btn btn-primary font-weight-bold">Create Vertical</button>
            </div>
        </form>
    </div>
</div>
@endsection
