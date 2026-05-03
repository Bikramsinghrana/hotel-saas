@extends('layouts.auth')

@section('auth-content')
<div class="auth-header">
    <h1 class="auth-title">{{ request()->routeIs('seller.login') ? 'Staff Login' : 'Welcome Back' }}</h1>
    <p class="auth-subtitle">Sign in to your account to continue</p>
</div>

<form method="POST" action="{{ request()->routeIs('seller.login') ? route('seller.login') : route('login') }}">
    @csrf
    
    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: baseline;">
            <label class="form-label">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link" style="font-size:0.8rem;">Forgot password?</a>
            @endif
        </div>
        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        @error('password')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group" style="display:flex; align-items:center; gap:0.5rem;">
        <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary);">
        <label for="remember" style="font-size:0.85rem; color:var(--text-muted); cursor:pointer;">Remember me</label>
    </div>

    <button type="submit" class="auth-btn">Sign In</button>
    
    @if (Route::has('register'))
        <div class="auth-links">
            Don't have an account? <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </div>
    @endif
</form>
@endsection
