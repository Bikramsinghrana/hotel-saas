@extends('layouts.admin')

@section('title', 'Blog Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">Blogs</h1>
            <p class="text-muted small mb-0">Publish and manage articles, news, and updates.</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Create Post</span>
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="fas fa-blog fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total Posts</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['published'] }}</h4>
                        <small class="text-muted">Live Posts</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card mb-0 h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['draft'] }}</h4>
                        <small class="text-muted">In Progress</small>
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
                        <th>Post</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($blog->featured_image)
                                        <img src="{{ asset('storage/' . $blog->featured_image) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $blog->title }}</div>
                                        <code class="smaller text-primary">/blog/{{ $blog->slug }}</code>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $blog->status->color() }}">{{ $blog->status->label() }}</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-active" type="checkbox" data-id="{{ $blog->id }}" {{ $blog->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-light btn-sm text-info"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-light btn-sm text-danger delete-btn" data-id="{{ $blog->id }}"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No blog posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blogs->hasPages())
            <div class="mt-4">{{ $blogs->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.toggle-active').forEach(btn => {
        btn.addEventListener('change', function() {
            fetch(`{{ route('admin.blogs.toggle-active', '') }}/${this.dataset.id}`, {
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
                title: 'Delete this post?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Delete'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('admin.blogs.destroy', '') }}/${this.dataset.id}`;
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
