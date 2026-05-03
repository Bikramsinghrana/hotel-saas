@extends('layouts.auth')

@section('auth-content')
<div class="auth-header">
    <h1 class="auth-title">Create Account</h1>
    <p class="auth-subtitle">Join us to book your perfect luxury stay</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-input" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}" required>
        @error('email')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" placeholder="Create a secure password" required>
        @error('password')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-input" placeholder="Confirm your password" required>
    </div>

    <button type="submit" class="auth-btn">Create Account</button>
    
    <div class="auth-links">
        Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign in instead</a>
    </div>
</form>
@endsection
