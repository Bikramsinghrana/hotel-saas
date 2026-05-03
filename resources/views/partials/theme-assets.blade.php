@php
    $themeSlug = $theme->slug ?? session('theme_slug') ?? 'hotel';
@endphp

<!-- Theme CSS -->
<link rel="stylesheet" href="{{ asset('css/themes/' . $themeSlug . '/app.css') }}">

<!-- SweetAlert2 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Common JS (toast, loader) -->
<script src="{{ asset('js/common.js') }}"></script>

<!-- Theme JS -->
<script src="{{ asset('js/themes/' . $themeSlug . '/app.js') }}" defer></script>
