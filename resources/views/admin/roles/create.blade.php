@extends('layouts.admin')

@section('header_title', 'Create Role')

@section('content')
<div style="max-width: 600px;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem;">&larr; Back</a>
        <h1 class="page-title" style="margin-bottom: 0;">Create New Role</h1>
    </div>

    <div class="admin-card">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="e.g. editor">
                @error('name')
                    <div style="color: #ef4444; font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <label class="form-label" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Assign Permissions</label>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    @foreach($permissions as $permission)
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" style="width: 16px; height: 16px; accent-color: var(--primary);">
                            {{ ucfirst($permission->name) }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
                <button type="submit" class="btn btn-primary">Create Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
