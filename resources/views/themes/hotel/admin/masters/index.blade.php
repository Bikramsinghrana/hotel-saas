@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@php
    $displayTitle = ucfirst(str_replace('_', ' ', $type));
@endphp

@section('title', 'Manage ' . $displayTitle)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">{{ $displayTitle }}</h1>
            <p class="text-muted small">Manage your hotel {{ strtolower($displayTitle) }} list.</p>
        </div>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#masterModal" onclick="resetForm()">
            <i class="fas fa-plus"></i>
            <span>Add New {{ $displayTitle }}</span>
        </button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">Icon</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masters as $master)
                        <tr>
                            <td>
                                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                                    <i class="{{ $master->icon_or_image ?? 'fas fa-check' }} text-primary"></i>
                                </div>
                            </td>
                            <td><div class="fw-bold text-dark">{{ $master->title }}</div></td>
                            <td><div class="text-muted small text-truncate" style="max-width: 250px;">{{ $master->content ?? 'No description' }}</div></td>
                            <td>
                                <span class="badge {{ $master->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($master->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-light btn-sm border" 
                                        onclick="editMaster({{ json_encode($master) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.masters.destroy', $master) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                No {{ strtolower($displayTitle) }} found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $masters->appends(['type' => $type])->links() }}
        </div>
    </div>
</div>

<!-- Unified Modal for Add/Edit -->
<div class="modal fade" id="masterModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="masterForm" action="{{ route('admin.masters.store') }}" method="POST" class="modal-content">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New {{ $displayTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" id="field_title" class="form-control" placeholder="e.g. Free Wi-Fi, Laundry, Gym" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description/Content</label>
                    <textarea name="content" id="field_content" class="form-control" rows="3" placeholder="Optional details..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Icon Class (FontAwesome)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i id="icon_preview" class="fas fa-check"></i></span>
                        <input type="text" name="icon_or_image" id="field_icon" class="form-control" placeholder="e.g. fas fa-wifi" onkeyup="updateIconPreview(this.value)">
                    </div>
                </div>
                <div class="mb-3" id="statusField" style="display:none;">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" id="field_status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save {{ $displayTitle }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeLabel = "{{ $displayTitle }}";
    const storeUrl = "{{ route('admin.masters.store') }}";

    function resetForm() {
        $('#masterForm').attr('action', storeUrl);
        $('#methodField').html('');
        $('#modalTitle').text('Add New ' + typeLabel);
        $('#saveBtn').text('Save ' + typeLabel);
        $('#statusField').hide();
        $('#masterForm')[0].reset();
        updateIconPreview('fas fa-check');
    }

    function editMaster(data) {
        $('#masterForm').attr('action', `/admin/masters/${data.id}`);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        $('#modalTitle').text('Edit ' + typeLabel);
        $('#saveBtn').text('Update ' + typeLabel);
        $('#statusField').show();
        
        $('#field_title').val(data.title);
        $('#field_content').val(data.content);
        $('#field_icon').val(data.icon_or_image);
        $('#field_status').val(data.status);
        
        updateIconPreview(data.icon_or_image);
        
        $('#masterModal').modal('show');
    }

    function updateIconPreview(val) {
        $('#icon_preview').attr('class', val || 'fas fa-check');
    }

    // Success Toast
    @if(session('success'))
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif
</script>
@endpush
