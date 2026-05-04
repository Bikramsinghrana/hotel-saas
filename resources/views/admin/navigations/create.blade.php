@extends('layouts.admin')

@section('title', 'Add Navigation Item')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.navigations.index') }}" class="btn btn-light btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <h1 class="page-title">Add Navigation Item</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="admin-card">
                <form action="{{ route('admin.navigations.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Home, About Us" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug (Internal)</label>
                            <input type="text" name="slug" class="form-control" placeholder="e.g. home">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL / Path</label>
                            <input type="text" name="url" class="form-control" placeholder="e.g. /about-us or #">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional description..."></textarea>
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
                        <div class="col-md-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                <label class="form-check-label" for="isActive">Enable this item immediately</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save Navigation Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card bg-light border-0">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Tips</h5>
                <ul class="small text-muted ps-3">
                    <li class="mb-2">Use <strong>URL</strong> for linking to specific pages (e.g. <code>/contact</code>).</li>
                    <li class="mb-2"><strong>Slug</strong> is used internally for themes.</li>
                    <li><strong>Order</strong> determines the position in the navbar (lower numbers appear first).</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
