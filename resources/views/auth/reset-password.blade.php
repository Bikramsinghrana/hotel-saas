@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-md mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Reset Password</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">
            <div class="mb-4">
                <label class="block text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm">New Password</label>
                <input type="password" name="password" required class="w-full p-2 border rounded" />
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" data-loader class="px-4 py-2 bg-indigo-600 text-white rounded" data-loading-text="Updating">Reset password</button>
            </div>
        </form>
    </div>
@endsection
