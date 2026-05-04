@extends('layouts.admin')

@section('title', 'Edit Navigation Item')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.navigations.index') }}" class="btn btn-light btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <h1 class="page-title">Edit Navigation: {{ $navigation->title }}</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="admin-card">
                <form action="{{ route('admin.navigations.update', $navigation->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $navigation->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slug (Internal)</label>
                            <input type="text" name="slug" class="form-control" value="{{ $navigation->slug }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">URL / Path</label>
                            <input type="text" name="url" class="form-control" value="{{ $navigation->url }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $navigation->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                @foreach(App\Enums\ModuleStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $navigation->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Display Order</label>
                            <input type="number" name="order" class="form-control" value="{{ $navigation->order }}">
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $navigation->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Enable this item</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">Update Navigation Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
