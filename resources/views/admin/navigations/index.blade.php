@extends('layouts.admin')

@section('title', 'Navigation Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">Navigation</h1>
            <p class="text-muted small mb-0">Manage your website's menu and navigation structure.</p>
        </div>
        <a href="{{ route('admin.navigations.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add Nav Item</span>
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fas fa-link fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total Links</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['published'] }}</h4>
                        <small class="text-muted">Published</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="fas fa-pencil-alt fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['draft'] }}</h4>
                        <small class="text-muted">Drafts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($navigations as $nav)
                        <tr>
                            <td><span class="badge bg-light text-dark">#{{ $nav->order }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $nav->title }}</div>
                                <small class="text-muted">{{ $nav->description }}</small>
                            </td>
                            <td><code class="text-primary small">{{ $nav->url }}</code></td>
                            <td>
                                <span class="badge bg-{{ $nav->status->color() }}">{{ $nav->status->label() }}</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-active" type="checkbox" data-id="{{ $nav->id }}" {{ $nav->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.navigations.edit', $nav->id) }}" class="btn btn-light btn-sm text-info"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-light btn-sm text-danger delete-btn" data-id="{{ $nav->id }}"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No navigation items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($navigations->hasPages())
            <div class="mt-4">{{ $navigations->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.toggle-active').forEach(btn => {
        btn.addEventListener('change', function() {
            fetch(`{{ route('admin.navigations.toggle-active', '') }}/${this.dataset.id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(data => {
                if(data.success) Swal.fire({ icon: 'success', title: 'Updated', timer: 1000, showConfirmButton: false });
            });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('admin.navigations.destroy', '') }}/${this.dataset.id}`;
                    form.innerHTML = '@csrf @method("DELETE")';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
