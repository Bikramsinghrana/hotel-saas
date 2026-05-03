@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-lg mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Your Profile</h1>

        <div class="mb-2"><strong>Name:</strong> {{ $user->name }}</div>
        <div class="mb-4"><strong>Email:</strong> {{ $user->email }}</div>

        <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit profile</a>
    </div>
@endsection
