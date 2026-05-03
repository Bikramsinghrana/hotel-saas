@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-md mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Log in</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm">Email</label>
                <input type="email" name="email" required class="w-full p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm">Password</label>
                <input type="password" name="password" required class="w-full p-2 border rounded" />
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" data-loader class="px-4 py-2 bg-blue-600 text-white rounded" data-loading-text="Signing in">Sign in</button>
                <a href="{{ route('password.request') }}" class="text-sm">Forgot password?</a>
            </div>
        </form>
    </div>
@endsection
