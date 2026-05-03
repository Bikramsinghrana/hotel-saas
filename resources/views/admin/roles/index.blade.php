@extends('layouts.admin')

@section('header_title', 'Role Management')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1 class="page-title" style="margin-bottom: 0;">Roles & Permissions</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">+ Create Role</a>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Permissions</th>
                <th style="width: 150px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td><strong>{{ ucfirst($role->name) }}</strong></td>
                <td>
                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                        @foreach($role->permissions as $permission)
                            <span style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                        @if($role->permissions->isEmpty())
                            <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">No permissions assigned</span>
                        @endif
                    </div>
                </td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Edit</a>
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #64748b;">No roles found in the database.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
