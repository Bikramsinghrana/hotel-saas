<nav class="navbar navbar-expand-lg sticky-top resto-navbar bg-dark navbar-dark border-bottom border-secondary shadow">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('resto.welcome') }}">
            <div class="resto-logo-icon">
                <i class="fas fa-utensils text-warning"></i>
            </div>
            <div>
                <span class="resto-brand-name">{{ $tenant ? $tenant->name : 'Urban Bistro & Dining' }}</span>
                <span class="badge bg-warning text-dark font-weight-bold ms-1 small" style="font-size: 0.65rem;">RESTO</span>
            </div>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#restoNavMenu" aria-controls="restoNavMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="restoNavMenu">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('resto.welcome') && !request()->route('subtheme') ? 'active text-warning fw-bold' : '' }}" href="{{ route('resto.welcome') }}">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="{{ route('resto.welcome') }}#featured-menu">
                        Menu & Catalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="{{ route('resto.welcome') }}#table-reservation">
                        Reserve a Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="{{ route('resto.welcome') }}#experience">
                        Ambience & Cellar
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dining Layouts
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border border-secondary rounded-3">
                        <li><a class="dropdown-item py-2" href="{{ route('resto.welcome', ['subtheme' => 'fine_dining']) }}"><i class="fas fa-wine-glass text-warning me-2"></i> Fine Dining & Bistro</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('resto.welcome', ['subtheme' => 'fast_food']) }}"><i class="fas fa-hamburger text-danger me-2"></i> Fast Food & Combos</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('resto.welcome', ['subtheme' => 'cafe']) }}"><i class="fas fa-coffee text-success me-2"></i> Artisan Cafe</a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(Auth::user()->hasRole(\App\Enums\RoleEnum::CUSTOMER->value) && Auth::user()->roles->count() === 1)
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-outline-light px-3">
                            <i class="fas fa-user-circle me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-sm btn-warning text-dark px-3 font-weight-bold">
                            <i class="fas fa-sliders-h me-1"></i> Resto Admin
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light px-3">Log in</a>
                    <a href="{{ route('resto.welcome') }}#table-reservation" class="btn btn-sm btn-warning text-dark px-3 font-weight-bold">
                        <i class="fas fa-calendar-check me-1"></i> Book Table
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
