{{-- Hotel & Hospitality Vertical Sidebar --}}
<li class="sidebar-nav-header">Hotel Operations</li>

@canAction('manage hotels')
<li>
    <a href="#hotelManagementSubmenu" 
       data-bs-toggle="collapse" 
       role="button" 
       aria-expanded="{{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.room-types.*', 'admin.terms.*', 'admin.coupons.*') ? 'true' : 'false' }}" 
       aria-controls="hotelManagementSubmenu"
       class="{{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.room-types.*', 'admin.terms.*', 'admin.coupons.*') ? 'active' : '' }}">
        <i class="fas fa-hotel"></i>
        <span>Property Master</span>
        <i class="fas fa-chevron-down ms-auto small" style="font-size: 0.7rem;"></i>
    </a>
    <ul class="collapse sidebar-submenu {{ request()->routeIs('admin.hotels.*', 'admin.masters.*', 'admin.terms.*', 'admin.room-types.*', 'admin.coupons.*') ? 'show' : '' }}" id="hotelManagementSubmenu">
        <li><a href="{{ route('admin.hotels.index') }}" class="{{ request()->routeIs('admin.hotels.index') ? 'active' : '' }}">Hotels & Resorts</a></li>
        <li><a href="{{ route('admin.room-types.index') }}" class="{{ request()->routeIs('admin.room-types.index') ? 'active' : '' }}">Accommodation Types</a></li>
        <li><a href="{{ route('admin.terms.index', ['type' => 'amenity']) }}" class="{{ request()->fullUrlIs(route('admin.terms.index', ['type' => 'amenity'])) ? 'active' : '' }}">Amenities</a></li>
        <li><a href="{{ route('admin.terms.index', ['type' => 'extra_service']) }}" class="{{ request()->fullUrlIs(route('admin.terms.index', ['type' => 'extra_service'])) ? 'active' : '' }}">Extra Services</a></li>
        <li><a href="{{ route('admin.terms.index', ['type' => 'facility']) }}" class="{{ request()->fullUrlIs(route('admin.terms.index', ['type' => 'facility'])) ? 'active' : '' }}">Facilities</a></li>
        <li><a href="{{ route('admin.coupons.index', ['type' => 'coupon']) }}" class="{{ request()->fullUrlIs(route('admin.coupons.index', ['type' => 'coupon'])) ? 'active' : '' }}">Coupons</a></li>
        <li><a href="{{ route('admin.coupons.index', ['type' => 'offer']) }}" class="{{ request()->fullUrlIs(route('admin.coupons.index', ['type' => 'offer'])) ? 'active' : '' }}">Exclusive Offers</a></li>
    </ul>
</li>
@endcanAction

@canAction('manage bookings')
<li>
    <a href="{{ route('admin.rooms.index') }}" class="{{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
        <i class="fas fa-bed"></i>
        <span>Rooms & Suites</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings & Guests</span>
    </a>
</li>
@endcanAction

<li>
    <a href="{{ route('admin.guests.index') }}" class="{{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">
        <i class="fas fa-user-check"></i>
        <span>Guest Directory</span>
    </a>
</li>
