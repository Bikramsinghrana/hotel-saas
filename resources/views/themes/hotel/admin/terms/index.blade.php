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
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#termModal" onclick="resetForm()">
            <i class="fas fa-plus"></i>
            <span>Add New {{ $displayTitle }}</span>
        </button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terms as $term)
                        <tr>
                            <td><div class="fw-bold text-dark">{{ $term->title }}</div></td>
                            <td>
                                @if($term->price > 0)
                                    <span class="badge bg-soft-success text-success">{{ $term->price_type }}{{ $term->price }}</span>
                                @else
                                    <span class="text-muted small">Free</span>
                                @endif
                            </td>
                            <td><div class="text-muted small text-truncate" style="max-width: 250px;">{{ $term->description ?? 'No description' }}</div></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-light btn-sm border" 
                                        onclick="editTerm({{ json_encode($term) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.terms.destroy', $term->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="4" class="text-center py-5 text-muted">
                                No {{ strtolower($displayTitle) }} found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $terms->appends(['type' => $type])->links() }}
        </div>
    </div>
</div>

<!-- Unified Modal for Add/Edit -->
<div class="modal fade" id="termModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="termForm" action="{{ route('admin.terms.store') }}" method="POST" class="modal-content">
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="number" name="price" id="field_price" class="form-control" step="0.01" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Currency/Type</label>
                        <input type="text" name="price_type" id="field_price_type" class="form-control" value="$">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" id="field_description" class="form-control" rows="3" placeholder="Optional details..."></textarea>
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
    const storeUrl = "{{ route('admin.terms.store') }}";

    function resetForm() {
        $('#termForm').attr('action', storeUrl);
        $('#methodField').html('');
        $('#modalTitle').text('Add New ' + typeLabel);
        $('#saveBtn').text('Save ' + typeLabel);
        $('#termForm')[0].reset();
        $('#field_price').val(0);
        $('#field_price_type').val('$');
    }

    function editTerm(data) {
        $('#termForm').attr('action', `/admin/terms/${data.id}`);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        $('#modalTitle').text('Edit ' + typeLabel);
        $('#saveBtn').text('Update ' + typeLabel);
        
        $('#field_title').val(data.title);
        $('#field_price').val(data.price);
        $('#field_price_type').val(data.price_type);
        $('#field_description').val(data.description);
        
        $('#termModal').modal('show');
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
