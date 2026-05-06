
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
            {{-- ONBOARDING: No industry selected --}}
            <li class="sidebar-nav-header">Onboarding</li>
            <li class="px-3 mb-3">
                <div class="alert alert-warning border-0 p-3 mb-0" style="background: rgba(250,204,21,0.1); border-radius: 12px;">
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <strong class="text-warning small">Setup Required</strong>
                    </div>
                    <p class="mb-0 text-warning" style="font-size: 0.75rem; line-height: 1.4;">
                        Select your industry to unlock the dashboard features.
                    </p>
                </div>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="setup-pulse {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-rocket"></i>
                    <span>Platform Setup</span>
                </a>
            </li>

        @elseif(!$subThemeIsConfigured)
            {{-- PARTIAL CONFIG: Main theme set, no layout --}}
            <li class="sidebar-nav-header">Onboarding</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="px-3 my-3">
                <div class="alert alert-info border-0 p-3 mb-0" style="background: rgba(59,130,246,0.1); border-radius: 12px;">
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <i class="fas fa-info-circle text-info"></i>
                        <strong class="text-info small">Step 2</strong>
                    </div>
                    <p class="mb-0 text-info" style="font-size: 0.75rem; line-height: 1.4;">
                        Industry selected! Now pick a <strong>layout</strong> to unlock all features.
                    </p>
                </div>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Choose Layout</span>
                </a>
            </li>

        @else
            {{-- FULLY CONFIGURED SIDEBAR --}}
            <li class="sidebar-nav-header">Main Menu</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @can('manage hotels')
            <li>
                <a href="#hotelManagementSubmenu" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.room-types.*') ? 'true' : 'false' }}" 
                   aria-controls="hotelManagementSubmenu"
                   class="{{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.room-types.*') ? 'active' : '' }}">
                    <i class="fas fa-hotel"></i>
                    <span>Hotel Management</span>
                    <i class="fas fa-chevron-down ms-auto small" style="font-size: 0.7rem;"></i>
                </a>
                <ul class="collapse sidebar-submenu {{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.room-types.*') ? 'show' : '' }}" id="hotelManagementSubmenu">
                    <li><a href="{{ route('admin.hotels.index') }}" class="{{ request()->routeIs('admin.hotels.index') ? 'active' : '' }}">Manage Hotels</a></li>
                    <li><a href="{{ route('admin.room-types.index') }}" class="{{ request()->routeIs('admin.room-types.index') ? 'active' : '' }}">Accommodation Types</a></li>
                    <li><a href="{{ route('admin.masters.index', ['type' => 'amenity']) }}" class="{{ request()->fullUrlIs(route('admin.masters.index', ['type' => 'amenity'])) ? 'active' : '' }}">Amenities</a></li>
                    <li><a href="{{ route('admin.masters.index', ['type' => 'guest_service']) }}" class="{{ request()->fullUrlIs(route('admin.masters.index', ['type' => 'guest_service'])) ? 'active' : '' }}">Guest Services</a></li>
                    <li><a href="{{ route('admin.masters.index', ['type' => 'room_facility']) }}" class="{{ request()->fullUrlIs(route('admin.masters.index', ['type' => 'room_facility'])) ? 'active' : '' }}">Room Facilities</a></li>
                    <li><a href="#">Feedback & Ratings</a></li>
                    <li><a href="#">Reservation</a></li>
                    <li><a href="#">Refund Report</a></li>
                </ul>
            </li>
            @endcan

            <li class="sidebar-nav-header">Management</li>
            
            @can('manage bookings')
            <li>
                <a href="#">
                    <i class="fas fa-bed"></i>
                    <span>Rooms</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            @endcan

            @can('manage sales')
            <li>
                <a href="#">
                    <i class="fas fa-tag"></i>
                    <span>Pricing</span>
                </a>
            </li>
            @endcan

            @can('manage hotels')
            <li>
                <a href="#">
                    <i class="fas fa-images"></i>
                    <span>Media</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Facilities</span>
                </a>
            </li>
            @endcan

            <li class="sidebar-nav-header">CMS & Content</li>
            
            @can('manage cms')
            <li>
                <a href="#webManagementSubmenu" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'true' : 'false' }}" 
                   aria-controls="webManagementSubmenu"
                   class="{{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'active' : '' }}">
                    <i class="fas fa-globe"></i>
                    <span>Web Management</span>
                    <i class="fas fa-chevron-down ms-auto small" style="font-size: 0.7rem;"></i>
                </a>
                <ul class="collapse sidebar-submenu {{ request()->routeIs('admin.navigations.*', 'admin.blogs.*', 'admin.sidebars.*') ? 'show' : '' }}" id="webManagementSubmenu">
                    <li>
                        <a href="{{ route('admin.navigations.index') }}" class="{{ request()->routeIs('admin.navigations.*') ? 'active' : '' }}">
                            Navigation
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                            Blogs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sidebars.index') }}" class="{{ request()->routeIs('admin.sidebars.*') ? 'active' : '' }}">
                            Sidebars
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-file-alt"></i>
                    <span>CMS Pages</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>
            </li>
            @endcan

            <li class="sidebar-nav-header">System</li>

            @if(!isSingleHotel())
                @can('manage users')
                <li>
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endcan
            @endif

            @can('manage roles')
            <li>
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            @endcan
        @endif
    </ul>
</aside>
