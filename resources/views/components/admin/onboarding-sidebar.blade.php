{{-- Merchant Onboarding & Layout Selection Sidebar --}}
@php
    $activeTenant = tenant();
    $themeIsConfigured = $activeTenant && $activeTenant->theme_id;
@endphp

@if(!$themeIsConfigured)
    <li class="sidebar-nav-header">Onboarding</li>
    <li class="px-3 mb-3">
        <div class="alert alert-warning border-0 p-3 mb-0 shadow-sm" style="background: rgba(245, 158, 11, 0.12); border-radius: 12px;">
            <div class="d-flex gap-2 align-items-center mb-1">
                <i class="fas fa-magic text-warning"></i>
                <strong class="text-warning small">Industry Setup</strong>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.78rem; line-height: 1.4;">
                Select your business industry to unlock vertical dashboard modules.
            </p>
        </div>
    </li>
    <li>
        <a href="{{ route('admin.settings.index') }}" class="setup-pulse {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-rocket text-primary"></i>
            <span>Select Industry</span>
        </a>
    </li>
@else
    <li class="sidebar-nav-header">Main Menu</li>
    <li>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-nav-header">Layout Setup</li>
    <li class="px-3 my-2">
        <div class="alert alert-info border-0 p-3 mb-0 shadow-sm" style="background: rgba(59, 130, 246, 0.12); border-radius: 12px;">
            <div class="d-flex gap-2 align-items-center mb-1">
                <i class="fas fa-brush text-info"></i>
                <strong class="text-info small">Step 2: Choose Layout</strong>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.78rem; line-height: 1.4;">
                Pick a storefront layout sub-theme to finalize your operational tools.
            </p>
        </div>
    </li>
    <li>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group text-info"></i>
            <span>Configure Layout</span>
        </a>
    </li>
@endif
