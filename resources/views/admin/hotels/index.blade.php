@extends('layouts.admin')

@section('title', 'Manage Hotels')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Manage Hotels</h1>
        <a href="{{ route('admin.hotels.wizard.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add New Hotel</span>
        </a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Hotel Details</th>
                        <th>Status</th>
                        <th>Pricing</th>
                        <th>Rating</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotels as $hotel)
                        <tr>
                            <td>
                                @php
                                    $thumb = $hotel->media->where('type', 'thumbnail')->first() ?? $hotel->media->first();
                                @endphp
                                <img src="{{ $thumb ? asset($thumb->path) : 'https://placehold.co/60x60?text=Hotel' }}" 
                                     class="rounded shadow-sm" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $hotel->name }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $hotel->address['city'] ?? 'City' }}, {{ $hotel->address['country'] ?? 'Country' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $hotel->status->value == 'active' ? 'bg-success' : ($hotel->status->value == 'draft' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ ucfirst($hotel->status->value) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">${{ number_format($hotel->base_price, 2) }}</div>
                                <div class="text-muted small">per night</div>
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i = 0; $i < $hotel->rating; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                    @for($i = $hotel->rating; $i < 5; $i++)
                                        <i class="far fa-star"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.hotels.wizard.edit', ['id' => $hotel->id]) }}">
                                                <i class="fas fa-edit me-2 text-primary"></i> Edit Hotel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-eye me-2 text-info"></i> View Details
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-hotel fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No hotels found. Start by adding your first hotel!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $hotels->links() }}
        </div>
    </div>
</div>
@endsection
