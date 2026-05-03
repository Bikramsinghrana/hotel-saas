
<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        Admin<span>Panel</span>
    </a>
    <ul class="sidebar-nav">
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
    </ul>
</aside>
