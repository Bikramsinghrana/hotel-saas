@extends('layouts.auth')

@section('auth-content')
<div class="auth-header">
    <h1 class="auth-title">Forgot Password</h1>
    <p class="auth-subtitle">Enter your email to receive a password reset link.</p>
</div>

@if (session('status'))
    <div class="auth-status">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    
    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="auth-btn">Send Reset Link</button>
    
    <div class="auth-links">
        Remember your password? <a href="{{ route('login') }}" class="auth-link">Sign in</a>
    </div>
</form>
@endsection
