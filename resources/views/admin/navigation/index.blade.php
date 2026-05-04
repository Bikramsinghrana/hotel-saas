@extends('layouts.admin')

@section('title', 'Manage Navigation')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Navigation Menu</h2>
            <p class="text-muted">Manage your website navigation items</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.navigation.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Navigation Item
            </a>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Navigation Items</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="navigations-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">Order</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Content/Description</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="navigations-body">
                    @forelse($navigations as $nav)
                        <tr data-id="{{ $nav->id }}" class="align-middle">
                            <td class="handle" style="cursor: grab;">
                                <i class="fas fa-arrows-alt-v text-muted"></i>
                                {{ $nav->order }}
                            </td>
                            <td>
                                <strong>{{ $nav->title }}</strong>
                            </td>
                            <td>
                                <code>{{ $nav->url }}</code>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($nav->content, 50) }}</small>
                            </td>
                            <td>
                                @if($nav->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.navigation.edit', $nav) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.navigation.destroy', $nav) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No navigation items found. <a href="{{ route('admin.navigation.create') }}">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($navigations->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $navigations->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Enable drag-and-drop reordering
    const el = document.getElementById('navigations-body');
    if (el) {
        Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateOrder();
            }
        });
    }

    function updateOrder() {
        const items = [];
        document.querySelectorAll('#navigations-body tr').forEach((row, index) => {
            items.push(row.dataset.id);
        });

        fetch('{{ route("admin.navigation.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items: items })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush
@endsection
