@extends('layouts.app')

@push('styles')
<style>
.auth-wrapper {
    min-height: calc(100vh - var(--nav-h));
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}
.auth-card {
    background: #fff;
    width: 100%;
    max-width: 480px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}
.auth-card::before {
    content: '';
    position: absolute; top: 0; left: 0; width: 100%; height: 5px;
    background: var(--primary);
}
.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}
.auth-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-bg);
    margin-bottom: 0.5rem;
}
.auth-subtitle, .auth-desc {
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.6;
}
.auth-desc {
    text-align: center;
    margin-bottom: 2rem;
}
.form-group {
    margin-bottom: 1.25rem;
}
.form-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--dark-card);
    margin-bottom: 0.4rem;
}
.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    transition: all .2s;
    background: #f8fafc;
}
.form-input:focus {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 4px var(--primary-light);
    outline: none;
}
.auth-btn {
    width: 100%;
    padding: 0.8rem;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    background: var(--primary);
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(22,163,74,.3);
    margin-top: 0.5rem;
}
.auth-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(22,163,74,.4);
}
.auth-links {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.85rem;
}
.auth-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}
.auth-link:hover { text-decoration: underline; }
.auth-error {
    color: #dc2626;
    font-size: 0.8rem;
    margin-top: 0.3rem;
}
.auth-status {
    background: var(--primary-light);
    color: var(--primary-dark);
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
    text-align: center;
}
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        @yield('auth-content')
    </div>
</div>
@endsection
