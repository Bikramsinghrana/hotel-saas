@extends('layouts.auth')

@section('auth-content')
<div class="auth-header">
    <h1 class="auth-title">Reset Password</h1>
    <p class="auth-subtitle">Create a new secure password</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token ?? '' }}">

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" value="{{ old('email', request()->email) }}" required readonly>
        @error('email')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" name="password" class="form-input" placeholder="Enter new password" required autofocus>
        @error('password')
            <div class="auth-error">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-input" placeholder="Confirm new password" required>
    </div>

    <button type="submit" class="auth-btn">Reset Password</button>
</form>
@endsection
