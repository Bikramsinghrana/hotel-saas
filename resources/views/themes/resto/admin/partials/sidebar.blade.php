{{-- Restaurant / Dining Vertical Sidebar --}}
<li class="sidebar-nav-header">Restaurant Operations</li>

@canAction('manage menus')
<li>
    <a href="#restoMenuSubmenu" 
       data-bs-toggle="collapse" 
       role="button" 
       aria-expanded="{{ request()->routeIs('admin.resto.menu.*', 'admin.resto.categories.*', 'admin.resto.addons.*') ? 'true' : 'false' }}" 
       aria-controls="restoMenuSubmenu"
       class="{{ request()->routeIs('admin.resto.menu.*', 'admin.resto.categories.*', 'admin.resto.addons.*') ? 'active' : '' }}">
        <i class="fas fa-utensils"></i>
        <span>Menu & Catalog</span>
        <i class="fas fa-chevron-down ms-auto small" style="font-size: 0.7rem;"></i>
    </a>
    <ul class="collapse sidebar-submenu {{ request()->routeIs('admin.resto.menu.*', 'admin.resto.categories.*', 'admin.resto.addons.*') ? 'show' : '' }}" id="restoMenuSubmenu">
        <li><a href="{{ route('admin.resto.menu.index') }}" class="{{ request()->routeIs('admin.resto.menu.index') ? 'active' : '' }}">Food & Drinks Menu</a></li>
        <li><a href="{{ route('admin.resto.categories.index') }}" class="{{ request()->routeIs('admin.resto.categories.*') ? 'active' : '' }}">Menu Categories</a></li>
        <li><a href="{{ route('admin.resto.addons.index') }}" class="{{ request()->routeIs('admin.resto.addons.*') ? 'active' : '' }}">Modifiers & Add-ons</a></li>
    </ul>
</li>
@endcanAction

@canAction('manage tables')
<li>
    <a href="{{ route('admin.resto.tables.index') }}" class="{{ request()->routeIs('admin.resto.tables.*') ? 'active' : '' }}">
        <i class="fas fa-chair"></i>
        <span>Tables & QR Dine-in</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.resto.reservations.index') }}" class="{{ request()->routeIs('admin.resto.reservations.*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i>
        <span>Table Reservations</span>
    </a>
</li>
@endcanAction

@canAction('manage orders')
<li>
    <a href="{{ route('admin.resto.pos.index') }}" class="{{ request()->routeIs('admin.resto.pos.*') ? 'active' : '' }}">
        <i class="fas fa-cash-register"></i>
        <span>POS Terminal</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.resto.orders.index') }}" class="{{ request()->routeIs('admin.resto.orders.*') ? 'active' : '' }}">
        <i class="fas fa-receipt"></i>
        <span>Live Orders & History</span>
    </a>
</li>
@endcanAction

@canAction('manage kitchen')
<li>
    <a href="{{ route('admin.resto.kds.index') }}" class="{{ request()->routeIs('admin.resto.kds.*') ? 'active' : '' }}">
        <i class="fas fa-fire-burner"></i>
        <span>Kitchen Display (KDS)</span>
    </a>
</li>
@endcanAction
