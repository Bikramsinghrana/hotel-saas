@extends('layouts.admin')

@section('title', 'Create Blog Post')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-light btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <h1 class="page-title">New Blog Post</h1>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="admin-card">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Post Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" placeholder="Enter title here..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content</label>
                        <textarea name="content" class="form-control" rows="15" placeholder="Write your content..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3" placeholder="Short summary for archives..."></textarea>
                    </div>
                </div>

                <div class="admin-card mt-4">
                    <h5 class="fw-bold mb-3">SEO Settings</h5>
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" placeholder="Browser title...">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" placeholder="Search engine description..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="admin-card">
                    <h5 class="fw-bold mb-3">Publish Settings</h5>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(App\Enums\ModuleStatusEnum::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label" for="isActive">Visible to Public</label>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Publish Post</button>
                </div>

                <div class="admin-card mt-4">
                    <h5 class="fw-bold mb-3">Featured Image</h5>
                    <div class="mb-0">
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                        <small class="text-muted mt-2 d-block">Recommended size: 1200x630px</small>
                    </div>
                </div>

                <div class="admin-card mt-4">
                    <h5 class="fw-bold mb-3">URL Slug</h5>
                    <div class="mb-0">
                        <input type="text" name="slug" class="form-control" placeholder="e.g. my-first-post">
                        <small class="text-muted mt-2 d-block">Leave blank to auto-generate from title.</small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
