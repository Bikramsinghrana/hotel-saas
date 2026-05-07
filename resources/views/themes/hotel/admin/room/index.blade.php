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
                <form action="{{ route('admin.rooms.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Add Random Room</span>
                    </button>
                </form>
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
                        <th>Room Name/Type</th>
                        <th>Status</th>
                        <th>Price</th>
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
                                <div class="fw-bold text-dark">{{ $room->room_type }}</div>
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
                            <td>
                                <div class="text-primary fw-bold">${{ number_format($room->price_per_day, 2) }}</div>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm border text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
