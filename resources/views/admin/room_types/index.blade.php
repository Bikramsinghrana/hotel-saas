@extends('layouts.admin')

@section('title', 'Manage Accommodation Types')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Accommodation Types</h1>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addRoomTypeModal">
            <i class="fas fa-plus"></i>
            <span>Add New Type</span>
        </button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Type Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomTypes as $type)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $type->room_type }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $type->status->value == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($type->status->value) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $type->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-light btn-sm border" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRoomTypeModal{{ $type->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.room-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm border text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editRoomTypeModal{{ $type->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.room-types.update', $type) }}" method="POST" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Accommodation Type</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Type Name</label>
                                            <input type="text" name="room_type" class="form-control" value="{{ $type->room_type }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" {{ $type->status->value == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $type->status->value == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Type</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                No accommodation types found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $roomTypes->links() }}
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.room-types.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Accommodation Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Type Name</label>
                    <input type="text" name="room_type" class="form-control" placeholder="e.g. Deluxe, Suite, Villa" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Type</button>
            </div>
        </form>
    </div>
</div>
@endsection
