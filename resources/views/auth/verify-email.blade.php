@extends('layouts.auth')

@section('auth-content')
<div class="auth-header">
    <h1 class="auth-title">Verify Email</h1>
</div>

<p class="auth-desc">
    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
</p>

@if (session('status') == 'verification-link-sent')
    <div class="auth-status">
        A new verification link has been sent to the email address you provided during registration.
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="auth-btn">Resend Verification Email</button>
</form>
@endsection
