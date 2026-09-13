@php
    $currentTenant = $tenant ?? tenant() ?? \App\Models\Tenant::whereHas('theme', fn($q) => $q->where('key', 'hotel'))->first() ?? \App\Models\Tenant::first();
@endphp
<nav class="navbar navbar-expand-lg sticky-top hotel-navbar bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('hotel.welcome') }}">
            <div class="hotel-logo-icon">
                <i class="fas fa-hotel text-primary"></i>
            </div>
            <span class="hotel-brand-name">{{ $currentTenant ? $currentTenant->name : 'Grand Palace Resort' }}</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#hotelNavMenu" aria-controls="hotelNavMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="hotelNavMenu">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                @php
                    $navItems = $navigations ?? collect();
                    if ($navItems->isEmpty() && class_exists(\App\Models\Navigation::class)) {
                        if ($currentTenant) {
                            $navItems = \App\Models\Navigation::active()->ordered()->where('tenant_id', $currentTenant->id)->get();
                        } else {
                            $navItems = collect();
                        }
                    }
                @endphp

                @forelse($navItems as $nav)
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->fullUrlIs(url($nav->url)) || request()->is(ltrim($nav->url, '/'))) ? 'active text-primary fw-bold' : '' }}" href="{{ $nav->url }}" title="{{ $nav->content }}">
                            {{ $nav->title }}
                        </a>
                    </li>
                @empty
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hotel.welcome') && !request()->route('subtheme') ? 'active text-primary fw-bold' : '' }}" href="{{ route('hotel.welcome') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('rooms.index') }}">
                            Rooms & Suites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('hotel.welcome') }}#why-choose-us">
                            Amenities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('hotel.welcome') }}#offers">
                            Offers & Deals
                        </a>
                    </li>
                @endforelse
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(Auth::user()->hasRole(\App\Enums\RoleEnum::CUSTOMER->value) && Auth::user()->roles->count() === 1)
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-outline-dark px-3">
                            <i class="fas fa-user-circle me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-sliders-h me-1"></i> Hotel Admin
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary px-3">
                        <i class="fas fa-sign-in-alt me-1"></i> Sign In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-success px-3">
                            <i class="fas fa-calendar-check me-1"></i> Book A Room
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
