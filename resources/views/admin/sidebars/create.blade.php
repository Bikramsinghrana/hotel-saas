@extends('layouts.admin')

@section('title', 'Add Sidebar Module')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.sidebars.index') }}" class="btn btn-light btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <h1 class="page-title">Add Sidebar Module</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="admin-card">
                <form action="{{ route('admin.sidebars.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Module Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Recent Posts, Contact Info" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Slug / Identifier</label>
                            <input type="text" name="slug" class="form-control" placeholder="e.g. recent-posts">
                            <small class="text-muted">Unique ID used in themes to render this specific module.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Content (HTML allowed)</label>
                            <textarea name="content" class="form-control" rows="10" placeholder="Enter the content or HTML for this sidebar module..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(App\Enums\ModuleStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="order" class="form-control" value="0">
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                <label class="form-check-label" for="isActive">Module is active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">Create Module</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card bg-light border-0">
                <h5 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> How to use</h5>
                <p class="small text-muted mb-3">
                    Sidebar modules are small blocks of content that appear on the side of your blog or pages.
                </p>
                <ul class="small text-muted ps-3">
                    <li class="mb-2"><strong>Title</strong> is for your reference in the admin panel.</li>
                    <li><strong>Slug</strong> must be unique and is used by the theme engine.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
