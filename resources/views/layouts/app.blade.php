<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hotel SaaS') }}</title>
    <meta name="description" content="Discover and book luxury hotels at the best prices. Compare rooms, read reviews and enjoy your perfect stay.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #16a34a;
            --primary-dark: #15803d;
            --primary-light: #dcfce7;
            --accent: #f59e0b;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --text-muted: #64748b;
            --nav-h: 72px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── NAV ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--nav-h);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark-bg);
            flex-shrink: 0;
        }

        .navbar-brand .brand-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
            margin-bottom: 2px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .navbar-nav a {
            display: block;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: color .2s, background .2s;
        }

        .navbar-nav a:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .btn-ghost {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            background: transparent;
            transition: all .2s;
        }

        .btn-ghost:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            background: var(--primary);
            border: 1.5px solid var(--primary);
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(22,163,74,.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 4px 14px rgba(22,163,74,.4);
            transform: translateY(-1px);
        }

        /* ── FOOTER ── */
        .site-footer {
            background: var(--dark-bg);
            color: #94a3b8;
            padding: 3rem 1.5rem 1.5rem;
            margin-top: 5rem;
        }

        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #1e293b;
        }

        @media (max-width: 768px) {
            .footer-top { grid-template-columns: 1fr 1fr; }
            .navbar-nav { display: none; }
        }

        @media (max-width: 480px) {
            .footer-top { grid-template-columns: 1fr; }
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
        }

        .footer-desc { font-size: 0.875rem; line-height: 1.6; color: #64748b; }

        .footer-heading {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 1rem;
        }

        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
        .footer-links a { color: #64748b; font-size: 0.875rem; text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--primary); }

        .footer-bottom {
            padding-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #475569;
        }
    </style>

    @stack('styles')
</head>
<body>

    @include('components.frontend.nav', ['navigations' => $navigations ?? collect()])

    <!-- CONTENT -->
    <main>
        @yield('content')
    </main>

    @include('components.frontend.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('common/js/common.js') }}"></script>
    @stack('scripts')
</body>
</html>
