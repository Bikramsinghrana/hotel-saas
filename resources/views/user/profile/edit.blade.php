@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-lg mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Edit Profile</h1>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm">New Password (leave blank to keep)</label>
                <input type="password" name="password" class="w-full p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full p-2 border rounded" />
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" data-loader class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
@endsection
