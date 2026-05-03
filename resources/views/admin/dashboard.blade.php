@extends('layouts.admin')

@section('header_title', 'Dashboard Overview')

@section('content')
<div class="page-title">Welcome back, {{ Auth::user()->name ?? 'Admin' }}</div>

<div class="admin-card">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Hotels</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0f172a;">{{ $hotels }}</div>
        </div>
        <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Bookings</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $bookings }}</div>
        </div>
        <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Users</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0f172a;">{{ $users }}</div>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">Quick Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        @can('manage users')
        <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary">Guest Listing</a>
        @endcan
        @can('manage roles')
        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">Manage Roles</a>
        @endcan
    </div>
</div>
@endsection
