@php
    $currentTenant = $tenant ?? tenant() ?? \App\Models\Tenant::whereHas('theme', fn($q) => $q->where('key', 'hotel'))->first() ?? \App\Models\Tenant::first();
@endphp
<nav class="navbar" id="main-nav">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">
            <span class="brand-dot"></span>
            {{ $currentTenant ? $currentTenant->name : config('app.name', 'Grand Palace Resort') }}
        </a>

        <ul class="navbar-nav">
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
                <li>
                    <a href="{{ $nav->url }}" title="{{ $nav->content }}" class="{{ (request()->fullUrlIs(url($nav->url)) || request()->is(ltrim($nav->url, '/'))) ? 'active' : '' }}">
                        {{ $nav->title }}
                    </a>
                </li>
            @empty
                <li><a href="/">Home</a></li>
                <li><a href="{{ route('rooms.index') }}">Rooms & Suites</a></li>
                <li><a href="/#why-choose-us">Amenities</a></li>
                <li><a href="/#offers">Offers</a></li>
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
                    <a href="{{ route('rooms.index') }}" class="btn-primary">Book Your Stay</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
