
@php
    $activeTenant = tenant();
    $themeIsConfigured = $activeTenant && $activeTenant->theme_id;
    $subThemeIsConfigured = $activeTenant && $activeTenant->sub_theme_id;
@endphp

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        Admin<span>Panel</span>
    </a>
    <ul class="sidebar-nav">

        @if(!$themeIsConfigured)
            {{-- ════════════════════════════════ --}}
            {{-- ONBOARDING: No industry selected --}}
            {{-- ════════════════════════════════ --}}
            <li style="padding: 1rem 1rem 0.5rem;">
                <div style="background: rgba(250,204,21,0.1); border: 1px solid rgba(250,204,21,0.4); border-radius: 8px; padding: 0.75rem; font-size: 0.8rem; color: #fbbf24; line-height: 1.4;">
                    ⚙️ <strong>Setup required</strong><br>
                    Select your industry to unlock the dashboard.
                </div>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}"
                   class="setup-pulse {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    🚀 Platform Setup
                </a>
            </li>

        @elseif(!$subThemeIsConfigured)
            {{-- ════════════════════════════════════════ --}}
            {{-- PARTIAL CONFIG: Main theme set, no layout --}}
            {{-- ════════════════════════════════════════ --}}
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li style="padding: 1rem 1rem 0.5rem;">
                <div style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3); border-radius: 8px; padding: 0.75rem; font-size: 0.8rem; color: #60a5fa; line-height: 1.4;">
                    📐 Industry selected! Now pick a <strong>layout</strong> to unlock all features.
                </div>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    ⚙️ Choose Layout
                </a>
            </li>

        @else
            {{-- ════════════════════════ --}}
            {{-- FULLY CONFIGURED SIDEBAR --}}
            {{-- ════════════════════════ --}}
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>

            @if(!isSingleHotel())
                @can('manage hotels')
                <li><a href="#">Hotels</a></li>
                @endcan
            @endif

            @can('manage bookings')
            <li><a href="#">Rooms</a></li>
            <li><a href="#">Bookings</a></li>
            @endcan

            @can('manage sales')
            <li><a href="#">Pricing</a></li>
            @endcan

            @can('manage hotels')
            <li><a href="#">Media</a></li>
            <li><a href="#">Facilities</a></li>
            @endcan

            @can('manage cms')
            <li><a href="#">CMS</a></li>
            <li><a href="#">Reviews</a></li>
            <li><a href="#">FAQ</a></li>
            @endcan

            @if(!isSingleHotel())
                @can('manage users')
                <li><a href="#">Users</a></li>
                @endcan
            @endif

            @can('manage roles')
            <li><a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">Settings</a></li>
            @endcan
        @endif

    </ul>
</aside>
