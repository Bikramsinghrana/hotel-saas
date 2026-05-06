@extends('hotel.layouts.admin')

@section('title', 'Manage Hotels')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">Manage Hotels</h1>
            <button id="bulkDeleteBtn" 
                    class="btn btn-outline-danger btn-sm mt-2" 
                    style="display: none;" 
                    data-url="{{ route('admin.hotels.bulk-delete') }}">
                <i class="fas fa-trash me-1"></i> Delete Selected
            </button>
        </div>
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
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th style="width: 80px;">Image</th>
                        <th>Hotel Details</th>
                        <th>Status</th>
                        <th>Pricing</th>
                        <th>Rooms</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotels as $hotel)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $hotel->id }}">
                            </td>
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
                                <div class="dropdown">
                                    <span class="badge cursor-pointer {{ $hotel->status->value == 'active' ? 'bg-success' : ($hotel->status->value == 'draft' ? 'bg-warning' : ($hotel->status->value == 'pending' ? 'bg-info' : 'bg-secondary')) }}" 
                                          data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                        {{ ucfirst($hotel->status->value) }}
                                    </span>
                                    <ul class="dropdown-menu shadow-sm border-0">
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $hotel->id }}, 'active')">Active</a></li>
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $hotel->id }}, 'inactive')">Inactive</a></li>
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $hotel->id }}, 'pending')">Pending</a></li>
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $hotel->id }}, 'draft')">Draft</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">${{ number_format($hotel->base_price, 2) }}</div>
                                <div class="text-muted small">per night</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.rooms.index', ['hotel_id' => $hotel->id]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-bed me-1"></i> {{ $hotel->rooms_count ?? $hotel->rooms()->count() }} Rooms
                                </a>
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
                                            <a class="dropdown-item" href="{{ route('admin.rooms.index', ['hotel_id' => $hotel->id]) }}">
                                                <i class="fas fa-door-open me-2 text-success"></i> Manage Rooms
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                No hotels found.
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

@push('scripts')
    <script src="{{ asset('js/hotel/admin/hotel-management.js') }}"></script>
@endpush
