@php
    // Determine active theme slug/key. TenantMiddleware stores the theme key in session('theme').
    $themeSlug = $theme->slug ?? session('theme') ?? config('app.theme') ?? 'hotel';
@endphp

<!-- Theme CSS (served from public/assets/themes/{theme}) -->
<link rel="stylesheet" href="{{ asset('assets/themes/' . $themeSlug . '/css/app.css') }}">

<!-- SweetAlert2 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Common JS (toast, loader) - public assets -->
<script src="{{ asset('assets/common/js/common.js') }}"></script>

<!-- Theme JS (served from public/assets/themes/{theme}) -->
<script src="{{ asset('assets/themes/' . $themeSlug . '/js/app.js') }}" defer></script>
