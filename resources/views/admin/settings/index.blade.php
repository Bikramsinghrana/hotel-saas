@extends('layouts.admin')

@section('header_title', 'Platform Settings')

@push('styles')
<style>
    .settings-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .settings-layout {
            grid-template-columns: 1fr;
        }
    }

    .settings-nav {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 1rem 0;
        height: max-content;
    }

    .settings-nav a {
        display: block;
        padding: 0.75rem 1.5rem;
        color: #334155;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .settings-nav a:hover {
        background: #f8fafc;
        color: var(--primary);
    }

    .settings-nav a.active {
        background: var(--primary-light);
        color: var(--primary-dark);
        border-left-color: var(--primary);
    }

    .theme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .theme-card {
        background: #fff;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }

    .theme-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: #cbd5e1;
    }

    .theme-card.active {
        border-color: var(--primary);
        box-shadow: 0 0 0 1px var(--primary);
    }

    .theme-preview {
        height: 140px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #e2e8f0;
        position: relative;
    }
    
    .theme-preview-icon {
        font-size: 3rem;
        color: #cbd5e1;
    }

    .theme-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--primary);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .theme-info {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .theme-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .theme-type {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .theme-desc {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 1.5rem;
        flex: 1;
    }
</style>
@endpush

@section('content')
<div class="settings-layout">
    
    <!-- Settings Navigation -->
    <aside class="settings-nav">
        <div style="padding: 0 1.5rem 0.5rem; font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Configuration</div>
        <a href="#" class="active">Themes & Layout</a>
        <a href="#">General Settings</a>
        
        @can('manage roles')
        <div style="padding: 1.5rem 1.5rem 0.5rem; font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Access</div>
        <a href="{{ route('admin.roles.index') }}">Roles & Permissions</a>
        @endcan
    </aside>

    <!-- Settings Content -->
    <div class="settings-content">
        
        <!-- Main Theme Selection -->
        <div class="admin-card">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Primary Industry</h2>
            <p style="color: #64748b; font-size: 0.9rem;">Select your primary industry. This determines what layouts and features are available.</p>

            <div class="theme-grid">
                @foreach($themes as $theme)
                    <div class="theme-card {{ $activeThemeId == $theme->id ? 'active' : '' }}" style="border-width: 2px;">
                        <div class="theme-preview" style="height: 100px;">
                            @if($activeThemeId == $theme->id)
                                <div class="theme-badge">Active</div>
                            @endif
                            <div class="theme-preview-icon">
                                {{ $theme->key == 'hotel' ? '🏨' : '🍽️' }}
                            </div>
                        </div>
                        <div class="theme-info" style="padding: 1rem;">
                            <div class="theme-title" style="font-size: 1.2rem; text-align: center;">{{ $theme->name }}</div>
                            <p style="text-align: center; font-size: 0.8rem; color: #64748b; margin-bottom: 1rem;">{{ $theme->description }}</p>
                            @if($activeThemeId != $theme->id)
                                <form method="POST" action="{{ route('admin.settings.theme.main.activate') }}" class="ajax-form">
                                    @csrf
                                    <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                    <button type="submit" class="btn btn-secondary" style="width: 100%;">Select Industry</button>
                                </form>
                            @else
                                <button disabled class="btn" style="width: 100%; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;">Currently Selected</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($activeThemeId)
        <!-- Sub Theme Selection -->
        <div class="admin-card" style="margin-top: 2rem;">
            @php $activeTheme = $themes->firstWhere('id', $activeThemeId); @endphp
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">{{ $activeTheme->name }} Layouts</h2>
            <p style="color: #64748b; font-size: 0.9rem;">Choose the specific layout and feature set for your {{ strtolower($activeTheme->name) }}.</p>

            <div class="theme-grid">
                @foreach($activeTheme->subThemes as $subTheme)
                    <div class="theme-card {{ $activeSubThemeId == $subTheme->id ? 'active' : '' }}">
                        <div class="theme-preview">
                            @if($activeSubThemeId == $subTheme->id)
                                <div class="theme-badge">Active</div>
                            @endif
                            <div class="theme-preview-icon">
                                @if($subTheme->type == 'single_hotel' || $subTheme->type == 'boutique')
                                    🏖️
                                @elseif($subTheme->type == 'multi_hotel')
                                    🏢
                                @else
                                    🍝
                                @endif
                            </div>
                        </div>
                        <div class="theme-info">
                            <div class="theme-title">{{ $subTheme->name }}</div>
                            <div class="theme-type">{{ str_replace('_', ' ', $subTheme->type) }}</div>
                            <div class="theme-desc">{{ $subTheme->description ?? 'No description available.' }}</div>
                            
                            @if($activeSubThemeId != $subTheme->id)
                                <form method="POST" action="{{ route('admin.settings.theme.activate') }}" class="ajax-form">
                                    @csrf
                                    <input type="hidden" name="sub_theme_id" value="{{ $subTheme->id }}">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Activate Layout</button>
                                </form>
                            @else
                                <button disabled class="btn" style="width: 100%; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;">Active Layout</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
