@extends('layouts.admin')

@section('header_title', $moduleTitle ?? 'Restaurant Operations')

@section('content')
<div class="container-fluid py-3">
    <div style="background: linear-gradient(135deg, #7c2d12 0%, #9a3412 50%, #c2410c 100%); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.15); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                <i class="{{ $moduleIcon ?? 'fas fa-utensils' }}"></i>
            </div>
            <div>
                <span class="badge bg-warning text-dark font-weight-bold mb-1">RESTAURANT & DINING VERTICAL</span>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #fff;">{{ $moduleTitle ?? 'Operational Module' }}</h1>
                <p style="color: #fed7aa; margin-top: 0.3rem; margin-bottom: 0; font-size: 0.95rem;">
                    {{ $description ?? 'Configure and manage restaurant operations in real-time.' }}
                </p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff;">
        <div class="card-body p-5 text-center">
            <div class="mb-3 text-warning" style="font-size: 3rem;">
                <i class="{{ $moduleIcon ?? 'fas fa-utensils' }}"></i>
            </div>
            <h3 class="font-weight-bold text-dark">{{ $moduleTitle }}</h3>
            <p class="text-muted mx-auto" style="max-width: 520px;">
                {{ $description }}
            </p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-1"></i> Return to Dashboard
                </a>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-primary px-4">
                    <i class="fas fa-sliders-h me-1"></i> Vertical Settings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
