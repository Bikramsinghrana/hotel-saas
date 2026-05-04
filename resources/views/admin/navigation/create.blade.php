@extends('layouts.admin')

@section('title', 'Create Navigation Item')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Create Navigation Item</h2>
            <p class="text-muted">Add a new navigation link to your website menu</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.navigation.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Navigation Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.navigation.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" placeholder="e.g., Home, About, Services" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" placeholder="e.g., /, /about, /services, https://example.com" 
                                   value="{{ old('url') }}" required>
                            <small class="form-text text-muted">Can be a relative path (/) or external URL</small>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Description/Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="3" 
                                      placeholder="Optional: Add a description or tooltip">{{ old('content') }}</textarea>
                            <small class="form-text text-muted">This appears as a tooltip on hover</small>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           id="order" name="order" placeholder="0" min="0"
                                           value="{{ old('order', 0) }}">
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label"></label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong> - Show in navigation menu
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Navigation Item
                            </button>
                            <a href="{{ route('admin.navigation.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">💡 Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Use <code>/</code> for home page</li>
                        <li class="mb-2">Use <code>/#section</code> to link to page sections</li>
                        <li class="mb-2">Full URLs work for external links</li>
                        <li class="mb-2">Set Order to control menu position</li>
                        <li class="mb-2">Uncheck Active to hide without deleting</li>
                        <li class="mb-2">Add descriptions for accessibility</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
