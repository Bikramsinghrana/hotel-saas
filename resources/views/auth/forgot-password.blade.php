@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-md mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Forgot Password</h1>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm">Email</label>
                <input type="email" name="email" required class="w-full p-2 border rounded" />
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" data-loader class="px-4 py-2 bg-yellow-600 text-white rounded" data-loading-text="Sending">Send reset link</button>
            </div>
        </form>
    </div>
@endsection
