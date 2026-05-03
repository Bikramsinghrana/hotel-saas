@extends('layouts.app')

@section('content')
    @include('partials.theme-assets')
    <div class="max-w-md mx-auto py-12 px-6">
        <h1 class="text-2xl font-semibold mb-4">Verify Your Email</h1>

        <p class="mb-4">Before continuing, please check your email for a verification link.</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="flex items-center justify-end">
                <button type="submit" data-loader class="px-4 py-2 bg-blue-600 text-white rounded" data-loading-text="Sending">Resend verification</button>
            </div>
        </form>
    </div>
@endsection
