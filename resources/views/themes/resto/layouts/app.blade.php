<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($tenant ? $tenant->name : 'Urban Bistro') . ' | Restaurant & Dining')</title>

    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --resto-primary: #ea580c;
            --resto-dark: #09090b;
            --resto-gold: #f59e0b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fafaf9;
            color: #1c1917;
        }
        .resto-brand-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
        }
        .resto-logo-icon {
            width: 38px;
            height: 38px;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('themes.resto.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('themes.resto.layouts.footer')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
