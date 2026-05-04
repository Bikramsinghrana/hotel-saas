
<nav class="navbar" id="main-nav">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">
            <span class="brand-dot"></span>
            {{ config('app.name', 'LuxuryBo') }}
        </a>

        <ul class="navbar-nav">
            @forelse($navigations ?? collect() as $nav)
                <li><a href="{{ $nav->url }}" title="{{ $nav->content }}">{{ $nav->title }}</a></li>
            @empty
                {{-- Fallback navigation if no dynamic navigation is configured --}}
                <li><a href="/">Home</a></li>
                <li><a href="/#rooms">Rooms</a></li>
                <li><a href="/#amenities">Amenities</a></li>
                <li><a href="/#book">Book Now</a></li>
            @endforelse
        </ul>

        <div class="navbar-actions">
            @auth
                @if(Auth::user()->hasRole(\App\Enums\RoleEnum::CUSTOMER->value) && Auth::user()->roles->count() === 1)
                    <a href="{{ route('customer.dashboard') }}" class="btn-ghost">Dashboard</a>
                @else
                    <a href="{{ url('/admin/dashboard') }}" class="btn-ghost">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-primary" style="cursor:pointer;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Book Your Stay</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
