{{-- Super Admin Platform Control Sidebar --}}
<li class="sidebar-nav-header" style="color: #f59e0b; font-weight: 700; letter-spacing: 0.05em;">
    <i class="fas fa-crown text-warning me-1"></i> Platform Control
</li>
<li>
    <a href="{{ route('superadmin.dashboard') }}" class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" style="background: rgba(245,158,11,0.1); color: #f59e0b; font-weight: 600;">
        <i class="fas fa-chart-line text-warning"></i>
        <span>Platform Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('superadmin.themes.index') }}" class="{{ request()->routeIs('superadmin.themes.*') ? 'active' : '' }}">
        <i class="fas fa-palette"></i>
        <span>Theme & Verticals</span>
    </a>
</li>
<li>
    <a href="{{ route('superadmin.plans.index') }}" class="{{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
        <i class="fas fa-cubes"></i>
        <span>Subscription Plans</span>
    </a>
</li>
<li>
    <a href="{{ route('superadmin.features.index') }}" class="{{ request()->routeIs('superadmin.features.*') ? 'active' : '' }}">
        <i class="fas fa-toggle-on"></i>
        <span>Feature Catalog</span>
    </a>
</li>
<li>
    <a href="{{ route('superadmin.tenants.index') }}" class="{{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
        <i class="fas fa-store"></i>
        <span>Merchant Accounts</span>
    </a>
</li>

<li class="sidebar-nav-header" style="color: #94a3b8; font-weight: 700; letter-spacing: 0.05em; margin-top: 1rem;">
    Merchant Preview Mode
</li>
<li>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-desktop"></i>
        <span>Merchant Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.options.index') }}" class="{{ request()->routeIs('admin.options.*') ? 'active' : '' }}">
        <i class="fas fa-cogs"></i>
        <span>Dynamic Options</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="fas fa-sliders-h"></i>
        <span>Active Theme Setup</span>
    </a>
</li>

