@php
    $tenant = tenant();
    $theme = $tenant?->theme;
    $subTheme = $tenant?->subTheme;
    $themeKey = $theme ? strtolower($theme->key) : 'hotel';
    $isHotel = ($themeKey === 'hotel');
    $isResto = in_array($themeKey, ['restaurant', 'resto']);
    $livePreviewUrl = $isResto ? url('/resto') : url('/hotel');
@endphp

{{-- Dynamic Theme-Aware CMS & Content Header --}}
<li class="sidebar-nav-header d-flex align-items-center justify-content-between">
    <span><i class="{{ $isHotel ? 'fas fa-hotel' : ($isResto ? 'fas fa-utensils' : 'fas fa-file-alt') }} me-1"></i>{{ $isHotel ? 'Hotel CMS & Content' : ($isResto ? 'Restaurant CMS & Content' : 'CMS & Content') }}</span>
</li>

@canAction('manage cms')
<li>
    <a href="#webManagementSubmenu" 
       data-bs-toggle="collapse" 
       role="button" 
       aria-expanded="{{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'true' : 'false' }}" 
       aria-controls="webManagementSubmenu"
       class="{{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'active' : '' }}">
        <i class="fas fa-globe"></i>
        <span>{{ $isHotel ? 'Hotel Site CMS' : ($isResto ? 'Dining Site CMS' : 'Site Management') }}</span>
        <i class="fas fa-chevron-down ms-auto small" style="font-size: 0.7rem;"></i>
    </a>
    <ul class="collapse sidebar-submenu {{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'show' : '' }}" id="webManagementSubmenu">
        <li>
            <a href="{{ route('admin.navigations.index') }}" class="{{ request()->routeIs('admin.navigations.*') ? 'active' : '' }}">
                <i class="fas fa-compass me-1"></i> Header Navigation
            </a>
        </li>
        <li>
            <a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper me-1"></i> {{ $isHotel ? 'Travel & Hotel Blogs' : ($isResto ? 'Food & Recipe Stories' : 'Blogs & Articles') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.sidebars.index') }}" class="{{ request()->routeIs('admin.sidebars.*') ? 'active' : '' }}">
                <i class="fas fa-columns me-1"></i> Sidebar Widgets
            </a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ $livePreviewUrl }}" target="_blank" class="d-flex align-items-center text-success-light" style="font-weight: 600;">
        <i class="fas fa-external-link-alt text-success"></i>
        <span>View Live Storefront</span>
        <span class="badge bg-success text-white ms-auto" style="font-size: 0.65rem; text-transform: uppercase;">
            {{ $subTheme?->name ?? ($isHotel ? 'Hotel' : 'Resto') }}
        </span>
    </a>
</li>
@endcanAction

<li class="sidebar-nav-header">Administration & System</li>

@canAction('manage roles')
<li>
    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <i class="fas fa-sliders-h"></i>
        <span>Theme & Settings</span>
    </a>
</li>
@endcanAction
