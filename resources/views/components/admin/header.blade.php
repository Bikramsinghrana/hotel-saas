
<header class="admin-header">
    <div class="header-title">
        <span style="font-weight: 600; font-size: 1.1rem;">
            @yield('header_title', 'Dashboard')
        </span>
    </div>
    <div class="header-actions">
        <a href="{{ url('/') }}" target="_blank" class="header-link">View Site &nearr;</a>
        
        <div class="user-menu">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
            
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ef4444; font-weight:600; cursor:pointer; font-size:0.85rem; margin-left:0.5rem;">Logout</button>
            </form>
        </div>
    </div>
</header>
