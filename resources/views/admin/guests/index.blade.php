@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold">Guests</h1>

    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Tenant</th>
                    <th class="px-4 py-3">Roles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $user)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->phone }}</td>
                    <td class="px-4 py-3">{{ $user->tenant?->name }}</td>
                    <td class="px-4 py-3">{{ $user->getRoleNames()->join(', ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $guests->links() }}</div>
</div>
@endsection
