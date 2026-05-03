@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <div class="mt-6 grid gap-6 sm:grid-cols-3">
        <div class="p-4 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Hotels</div>
            <div class="text-2xl font-semibold">{{ $hotels }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Bookings</div>
            <div class="text-2xl font-semibold">{{ $bookings }}</div>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Users</div>
            <div class="text-2xl font-semibold">{{ $users }}</div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-semibold">Quick Links</h2>
        <div class="mt-4 flex gap-4">
            <a href="{{ route('admin.guests.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Guest Listing</a>
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Manage Roles</a>
        </div>
    </div>
</div>
@endsection
