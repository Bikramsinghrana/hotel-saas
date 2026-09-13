@php
    $activeTenant = tenant();
    $themeIsConfigured = $activeTenant && $activeTenant->theme_id;
    $subThemeIsConfigured = $activeTenant && $activeTenant->sub_theme_id;
    $themeKey = ($activeTenant && $activeTenant->theme) ? ($activeTenant->theme->key ?? 'hotel') : 'hotel';
    $themeFolder = ($themeKey === 'restaurant') ? 'resto' : $themeKey;
@endphp

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        @if($activeTenant && $activeTenant->theme)
            <i class="{{ $activeTenant->theme->icon ?? 'fas fa-store' }} text-primary me-2"></i>
            <span>{{ Str::limit($activeTenant->name, 14, '..') }}</span>
        @else
            Admin<span>Panel</span>
        @endif
    </a>
    
    <ul class="sidebar-nav">
        @if(is_super_admin())
            <li class="sidebar-nav-header" style="color: #f59e0b; font-weight: 700; letter-spacing: 0.05em;">
                <i class="fas fa-crown text-warning me-1"></i> Platform Control
            </li>
            <li>
                <a href="{{ route('superadmin.dashboard') }}" class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" style="background: rgba(245,158,11,0.1); color: #f59e0b; font-weight: 600;">
                    <i class="fas fa-shield-alt text-warning"></i>
                    <span>Super Admin Studio</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.themes.index') }}" class="{{ request()->routeIs('superadmin.themes.*') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i>
                    <span>Manage Themes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.plans.index') }}" class="{{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Plans & Features</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.tenants.index') }}" class="{{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span>Merchant Tenants</span>
                </a>
            </li>
        @endif

        @if(!$themeIsConfigured || !$subThemeIsConfigured)
            {{-- Incomplete Onboarding or Layout Setup --}}
            @include('components.admin.onboarding-sidebar')
        @else
            {{-- Fully Configured Vertical Dashboard --}}
            <li class="sidebar-nav-header">Main Menu</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Vertical-Specific Operations Partial --}}
            @if(view()->exists("themes.{$themeFolder}.admin.partials.sidebar"))
                @include("themes.{$themeFolder}.admin.partials.sidebar")
            @elseif(view()->exists("themes.{$themeKey}.admin.partials.sidebar"))
                @include("themes.{$themeKey}.admin.partials.sidebar")
            @else
                @include('themes.hotel.admin.partials.sidebar')
            @endif

            {{-- Cross-vertical Common Modules (CMS, System, Settings) --}}
            @include('components.admin.common-sidebar-links')
        @endif
    </ul>
</aside>
