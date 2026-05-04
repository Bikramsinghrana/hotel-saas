@extends('layouts.admin')

@section('title', 'Edit Sidebar Module')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.sidebars.index') }}" class="btn btn-light btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <h1 class="page-title">Edit Module: {{ $sidebar->title }}</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="admin-card">
                <form action="{{ route('admin.sidebars.update', $sidebar->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Module Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $sidebar->title }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Slug / Identifier</label>
                            <input type="text" name="slug" class="form-control" value="{{ $sidebar->slug }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Content (HTML allowed)</label>
                            <textarea name="content" class="form-control" rows="10">{{ $sidebar->content }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(App\Enums\ModuleStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $sidebar->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="order" class="form-control" value="{{ $sidebar->order }}">
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $sidebar->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Module is active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">Update Module</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
