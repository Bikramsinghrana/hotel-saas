@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Manage Rooms - ' . $hotel->name)

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
                <li class="breadcrumb-item active">{{ $hotel->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title mb-0">Rooms for {{ $hotel->name }}</h1>
            <div class="d-flex gap-2">
                <button id="bulkDeleteBtn" 
                        class="btn btn-outline-danger btn-sm" 
                        style="display: none;" 
                        data-url="{{ route('admin.rooms.bulk-delete') }}">
                    <i class="fas fa-trash me-1"></i> Delete Selected
                </button>
                <a href="{{ route('admin.rooms.wizard.create', ['hotel_id' => $hotel->id]) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Add New Room</span>
                </a>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Slug / Name</th>
                        <th>Rooms</th>
                        <th>Price</th>
                        <th>Member Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $room->id }}">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $room->room_slug ?? $room->room_type }}</div>
                            </td>
                            <td>
                                <div>{{ $room->total_rooms }}</div>
                            </td>
                            <td>
                                <div class="text-primary fw-bold">${{ number_format($room->price_per_day, 2) }}</div>
                            </td>
                            <td>
                                <div class="text-muted">${{ number_format($room->member_price, 2) }}</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <span class="badge cursor-pointer {{ $room->status == 'active' ? 'bg-success' : 'bg-secondary' }}" 
                                          data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                    <ul class="dropdown-menu shadow-sm border-0">
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $room->id }}, 'active', 'room')">Active</a></li>
                                        <li><a class="dropdown-item small" href="javascript:void(0)" onclick="changeStatus({{ $room->id }}, 'inactive', 'room')">Inactive</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Action (could point to frontend room view if it exists, or just #) -->
                                    <a href="#" class="btn btn-light btn-sm border text-info" title="View Room">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Action -->
                                    <a href="{{ route('admin.rooms.wizard.edit', $room->id) }}" class="btn btn-light btn-sm border text-primary" title="Edit Room">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <!-- Delete Action -->
                                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm border text-danger" title="Delete Room">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No rooms generated for this hotel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $rooms->appends(['hotel_id' => $hotelId])->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ \App\Helpers\HotelPath::asset('js/admin/hotel-management.js') }}"></script>
@endpush
