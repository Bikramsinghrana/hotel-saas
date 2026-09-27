@extends('layouts.admin')

@section('title', 'Dynamic Options & Configuration')
@section('header_title', 'Dynamic Options & Multi-Management Configuration')

@push('styles')
<style>
.options-dashboard {
    margin-bottom: 3rem;
}
.options-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 1.5rem;
}
@media (max-width: 992px) {
    .options-layout {
        grid-template-columns: 1fr;
    }
}
.category-nav {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e2e8f0;
    padding: 1rem;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
}
.category-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 0.75rem;
    color: #475569;
    font-weight: 600;
    font-size: 0.925rem;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-bottom: 0.35rem;
}
.category-link i {
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
    color: #94a3b8;
    transition: color 0.2s ease;
}
.category-link:hover {
    background: #f8fafc;
    color: #0f172a;
}
.category-link.active {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}
.category-link.active i {
    color: #ffffff;
}
.category-link.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}
.option-card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.option-row {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
}
.option-row:last-child {
    border-bottom: none;
}
.option-row:hover {
    background-color: #fafbfd;
}
.option-label {
    font-weight: 700;
    font-size: 0.975rem;
    color: #0f172a;
    margin-bottom: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.option-key {
    font-family: var(--bs-font-monospace, monospace);
    font-size: 0.775rem;
    color: #64748b;
    background: #f1f5f9;
    padding: 0.15rem 0.45rem;
    border-radius: 0.25rem;
}
.option-desc {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0;
    line-height: 1.4;
}
.form-switch .form-check-input {
    width: 2.75rem;
    height: 1.4rem;
    cursor: pointer;
}
.form-switch .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}
.btn-save-settings {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-weight: 700;
    border: none;
    border-radius: 0.75rem;
    padding: 0.75rem 2rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    transition: all 0.2s ease;
}
.btn-save-settings:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
}
.scope-pill {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 1rem;
}
</style>
@endpush

@section('content')
<div class="options-dashboard">
    <!-- Header with Breadcrumb & New Option Button -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">
                <i class="fas fa-sliders-h text-primary me-2"></i> Dynamic Options & Configuration
            </h2>
            <p class="text-muted small mb-0">
                Manage dynamic system settings, taxes, pagination, hotel policies, and restaurant parameters.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateOption">
                <i class="fas fa-plus-circle"></i>
                <span>Add Dynamic Option</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Context Switcher (For SuperAdmin / Multi-Hotel Tenants) -->
    <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
        <form method="GET" action="{{ route('admin.options.index') }}" id="filterForm" class="row g-3 align-items-center">
            <input type="hidden" name="group" value="{{ $activeGroup }}">
            
            @if($isSuperAdmin && $allTenants->isNotEmpty())
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-building text-primary me-1"></i> Tenant / Account</label>
                    <select name="tenant_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Global Platform Defaults (All Tenants) --</option>
                        @foreach($allTenants as $t)
                            <option value="{{ $t->id }}" {{ (string)$selectedTenantId === (string)$t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ ucfirst($t->theme->name ?? 'Hotel') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($hotels->isNotEmpty())
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-hotel text-success me-1"></i> Specific Property / Branch</label>
                    <select name="hotel_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- All Properties / General Tenant Settings --</option>
                        @foreach($hotels as $h)
                            <option value="{{ $h->id }}" {{ (string)$selectedHotelId === (string)$h->id ? 'selected' : '' }}>
                                {{ $h->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-{{ ($isSuperAdmin && $allTenants->isNotEmpty()) ? '4' : ($hotels->isNotEmpty() ? '8' : '12') }}">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search text-muted me-1"></i> Search Option</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search by key, label or description..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.options.index', ['group' => $activeGroup, 'tenant_id' => $selectedTenantId, 'hotel_id' => $selectedHotelId]) }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Main Layout: Navigation Sidebar + Options Form -->
    <div class="options-layout">
        <!-- Categories Sidebar -->
        <aside>
            <div class="category-nav">
                <div class="small fw-bold text-uppercase text-muted px-3 py-2 letter-spacing-1">
                    Option Categories
                </div>
                @foreach($groups as $grpKey => $grpMeta)
                    @php
                        $count = ($grpKey === 'all') 
                            ? $options->count() 
                            : ($groupedOptions->has($grpKey) ? $groupedOptions->get($grpKey)->count() : 0);
                    @endphp
                    <a href="{{ route('admin.options.index', ['group' => $grpKey, 'tenant_id' => $selectedTenantId, 'hotel_id' => $selectedHotelId, 'search' => request('search')]) }}" 
                       class="category-link {{ $activeGroup === $grpKey ? 'active' : '' }}">
                        <i class="{{ $grpMeta['icon'] }}"></i>
                        <span class="flex-grow-1">{{ $grpMeta['title'] }}</span>
                        @if($count > 0)
                            <span class="badge bg-light text-dark rounded-pill">{{ $count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </aside>

        <!-- Options Content -->
        <main>
            <form method="POST" action="{{ route('admin.options.batch-update') }}" id="batchOptionsForm">
                @csrf
                <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                <input type="hidden" name="hotel_id" value="{{ $selectedHotelId }}">

                @if($options->isEmpty())
                    <div class="option-card p-5 text-center">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-sliders-h fa-2x text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Options Found</h5>
                        <p class="text-muted small mb-3">There are no options defined for this category yet.</p>
                        <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalCreateOption">
                            <i class="fas fa-plus me-1"></i> Add First Option
                        </button>
                    </div>
                @else
                    @php
                        $displayGroups = ($activeGroup === 'all') 
                            ? $groupedOptions 
                            : collect([$activeGroup => $options]);
                    @endphp

                    @foreach($displayGroups as $groupKey => $groupItems)
                        @php
                            $groupMeta = $groups[$groupKey] ?? ['title' => ucfirst(str_replace('_', ' ', $groupKey)), 'icon' => 'fas fa-cog', 'desc' => ''];
                        @endphp

                        <div class="option-card">
                            <div class="p-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $groupMeta['icon'] }} text-primary"></i>
                                    <h5 class="fw-bold text-dark mb-0">{{ $groupMeta['title'] }}</h5>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">{{ $groupItems->count() }} Option(s)</span>
                            </div>

                            @foreach($groupItems as $opt)
                                <div class="option-row" id="optionRow_{{ $opt->id }}">
                                    <div class="row align-items-center g-3">
                                        <!-- Label & Key -->
                                        <div class="col-lg-6 col-md-5">
                                            <div class="option-label">
                                                <span>{{ $opt->label ?: ucfirst(str_replace('_', ' ', $opt->key)) }}</span>
                                                <span class="option-key">{{ $opt->key }}</span>
                                                @if($opt->is_public)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle small" title="Exposed to frontend scripts">Public</span>
                                                @endif
                                            </div>
                                            @if($opt->description)
                                                <p class="option-desc">{{ $opt->description }}</p>
                                            @endif
                                        </div>

                                        <!-- Value Input based on Type -->
                                        <div class="col-lg-5 col-md-5">
                                            @if($opt->type === 'boolean')
                                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                                    <input type="hidden" name="options[{{ $opt->key }}]" value="0">
                                                    <input class="form-check-input" type="checkbox" role="switch" 
                                                           name="options[{{ $opt->key }}]" value="1" 
                                                           id="switch_{{ $opt->id }}" 
                                                           {{ $opt->typed_value ? 'checked' : '' }}
                                                           onchange="quickUpdateOption('{{ $opt->key }}', this.checked ? '1' : '0')">
                                                    <label class="form-check-label small fw-semibold text-muted" for="switch_{{ $opt->id }}">
                                                        {{ $opt->typed_value ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>

                                            @elseif($opt->type === 'number' || $opt->type === 'float')
                                                <div class="input-group input-group-sm" style="max-width: 220px;">
                                                    <input type="number" name="options[{{ $opt->key }}]" 
                                                           class="form-control" 
                                                           value="{{ $opt->value }}" 
                                                           step="{{ $opt->type === 'float' ? '0.01' : '1' }}">
                                                    @if(str_contains($opt->key, 'rate') || str_contains($opt->key, 'percent') || str_contains($opt->key, 'tax') || str_contains($opt->key, 'gst'))
                                                        <span class="input-group-text bg-light text-muted">%</span>
                                                    @elseif(str_contains($opt->key, 'hours'))
                                                        <span class="input-group-text bg-light text-muted">Hrs</span>
                                                    @elseif(str_contains($opt->key, 'minutes'))
                                                        <span class="input-group-text bg-light text-muted">Min</span>
                                                    @endif
                                                </div>

                                            @elseif($opt->type === 'select')
                                                <select name="options[{{ $opt->key }}]" class="form-select form-select-sm" style="max-width: 260px;">
                                                    @if(!empty($opt->options_list) && is_array($opt->options_list))
                                                        @foreach($opt->options_list as $choiceKey => $choiceVal)
                                                            <option value="{{ $choiceKey }}" {{ (string)$opt->value === (string)$choiceKey ? 'selected' : '' }}>
                                                                {{ $choiceVal }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="inclusive" {{ $opt->value === 'inclusive' ? 'selected' : '' }}>Inclusive</option>
                                                        <option value="exclusive" {{ $opt->value === 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                                                    @endif
                                                </select>

                                            @elseif($opt->type === 'color')
                                                <div class="d-flex align-items-center gap-2" style="max-width: 200px;">
                                                    <input type="color" name="options[{{ $opt->key }}]" class="form-control form-control-color form-control-sm" value="{{ $opt->value ?: '#2563eb' }}">
                                                    <input type="text" class="form-control form-control-sm font-monospace" value="{{ $opt->value ?: '#2563eb' }}" readonly style="width: 90px;">
                                                </div>

                                            @elseif($opt->type === 'json' || $opt->type === 'array')
                                                <textarea name="options[{{ $opt->key }}]" class="form-control font-monospace small" rows="2" placeholder="JSON content">{{ is_array($opt->typed_value) ? json_encode($opt->typed_value, JSON_PRETTY_PRINT) : $opt->value }}</textarea>

                                            @else
                                                <input type="text" name="options[{{ $opt->key }}]" class="form-control form-control-sm" value="{{ $opt->value }}" placeholder="Enter setting value...">
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-lg-1 col-md-2 text-end">
                                            @if(!$opt->is_system)
                                                <button type="button" class="btn btn-link text-danger p-0" title="Delete custom option" onclick="deleteOption({{ $opt->id }}, '{{ $opt->key }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @else
                                                <span class="text-muted" title="System core option (Locked)"><i class="fas fa-lock"></i></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <!-- Save Floating / Footer Action Bar -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white border rounded-4 shadow-sm mt-3">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle text-primary me-1"></i> Changes apply immediately and invalidate the cache.
                        </div>
                        <button type="submit" class="btn btn-save-settings">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                @endif
            </form>
        </main>
    </div>
</div>

<!-- Modal: Create Dynamic Option -->
<div class="modal fade" id="modalCreateOption" tabindex="-1" aria-labelledby="modalCreateOptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalCreateOptionLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i> Create Dynamic Option
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.options.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                <input type="hidden" name="hotel_id" value="{{ $selectedHotelId }}">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Option Key <span class="text-danger">*</span></label>
                            <input type="text" name="key" class="form-control form-control-sm font-monospace" placeholder="e.g. table_tax_rate" required pattern="[a-zA-Z0-9_]+">
                            <small class="text-muted" style="font-size: 0.75rem;">Only letters, numbers, and underscores.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Display Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Table Tax Rate (%)" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category Group <span class="text-danger">*</span></label>
                            <select name="group" class="form-select form-select-sm" required>
                                <option value="hotel">Hotel & Booking</option>
                                <option value="restaurant">Restaurant & Dining</option>
                                <option value="tax_gst">Tax & GST System</option>
                                <option value="pagination">Pagination & Limits</option>
                                <option value="currency">Currency & Localization</option>
                                <option value="invoice">Invoicing & Billing</option>
                                <option value="general" selected>General Information</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data Type <span class="text-danger">*</span></label>
                            <select name="type" id="modalOptionType" class="form-select form-select-sm" required onchange="toggleSelectOptionsInput(this.value)">
                                <option value="string" selected>Text (String)</option>
                                <option value="number">Integer Number</option>
                                <option value="float">Decimal / Percent (Float)</option>
                                <option value="boolean">Toggle Switch (Boolean)</option>
                                <option value="select">Dropdown Choices (Select)</option>
                                <option value="color">Color Hex</option>
                                <option value="json">JSON / Array</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Initial Value</label>
                            <input type="text" name="value" class="form-control form-control-sm" placeholder="Enter default value...">
                        </div>

                        <div class="col-12" id="optionsListGroup" style="display: none;">
                            <label class="form-label small fw-bold">Dropdown Options List (comma-separated key:label)</label>
                            <input type="text" name="options_list_raw" class="form-control form-control-sm" placeholder="e.g. opt1:Option One, opt2:Option Two">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Description / Tooltip</label>
                            <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Explain what this configuration controls..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_autoload" value="1" id="switchAutoload" checked>
                                <label class="form-check-label small fw-semibold" for="switchAutoload">Autoload in Cache</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_public" value="1" id="switchPublic">
                                <label class="form-check-label small fw-semibold" for="switchPublic">Expose to Frontend API</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Create Option</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteOptionForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleSelectOptionsInput(type) {
    const listGrp = document.getElementById('optionsListGroup');
    listGrp.style.display = (type === 'select') ? 'block' : 'none';
}

function quickUpdateOption(key, value) {
    const tenantId = "{{ $selectedTenantId }}";
    const hotelId = "{{ $selectedHotelId }}";

    fetch("{{ route('admin.options.update-single') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            key: key,
            value: value,
            tenant_id: tenantId || null,
            hotel_id: hotelId || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || 'Saved',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(err => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Failed to update setting',
            timer: 2500,
            showConfirmButton: false
        });
    });
}

function deleteOption(id, key) {
    Swal.fire({
        title: 'Delete Option?',
        text: `Are you sure you want to delete option "${key}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteOptionForm');
            form.action = `{{ url('admin/options') }}/${id}`;
            form.submit();
        }
    });
}
</script>
@endpush
