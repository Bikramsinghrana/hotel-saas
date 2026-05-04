<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hotel SaaS') }} - Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Font Awesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #16a34a;
            --primary-dark: #15803d;
            --primary-light: #dcfce7;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #16a34a;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --header-h: 70px;
            --sidebar-w: 280px;
            --body-bg: #f8fafc;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--body-bg);
            color: #334155;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 1050;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        /* Onboarding pulse animation for the "Platform Setup" link */
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(250,204,21,0.5); }
            50% { box-shadow: 0 0 0 6px rgba(250,204,21,0); }
        }
        .setup-pulse {
            animation: pulse-glow 1.8s ease-in-out infinite;
            background: rgba(250,204,21,0.12) !important;
            color: #fbbf24 !important;
            border: 1px solid rgba(250,204,21,0.35);
        }
        .setup-pulse:hover {
            background: rgba(250,204,21,0.2) !important;
            color: #fbbf24 !important;
        }
        .sidebar-brand {
            height: var(--header-h);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none !important;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            letter-spacing: -0.5px;
        }
        .sidebar-brand span { color: var(--primary); }
        
        .sidebar-nav { padding: 1.5rem 0.75rem; list-style: none; margin: 0; }
        .sidebar-nav-header {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
        }
        .sidebar-nav li { margin-bottom: 0.125rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none !important;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            gap: 0.75rem;
        }
        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.7;
            transition: all 0.2s;
        }
        .sidebar-nav a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar-nav a:hover i { opacity: 1; transform: scale(1.1); }
        .sidebar-nav a.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
        }
        .sidebar-nav a.active i { opacity: 1; }

        /* Submenu styling */
        .sidebar-submenu {
            list-style: none;
            padding-left: 2.75rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }
        .sidebar-submenu a {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            opacity: 0.8;
        }
        .sidebar-submenu a:hover { opacity: 1; background: transparent; color: var(--primary); }
        .sidebar-submenu a.active { background: transparent; color: var(--primary); font-weight: 700; }

        /* MAIN CONTENT */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-w));
        }

        /* HEADER */
        .admin-header {
            height: var(--header-h);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-actions { display: flex; align-items: center; gap: 1.5rem; }
        .header-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .header-link:hover { color: var(--primary); }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 50px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .user-menu:hover { background: #f1f5f9; }
        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            box-shadow: 0 2px 5px rgba(22, 163, 74, 0.2);
        }

        /* CONTENT */
        .admin-content {
            padding: 2.5rem;
            flex: 1;
        }
        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        /* CARDS / TABLES */
        .admin-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 4px 6px -1px rgba(0,0,0,0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        /* Custom overrides for Bootstrap components in Admin */
        .badge { font-weight: 600; padding: 0.4em 0.8em; border-radius: 6px; }
        .table { --bs-table-hover-bg: #f8fafc; }
        .table thead th { 
            background: #f8fafc; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            color: #64748b; 
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }
        .table td { padding: 1rem; vertical-align: middle; }
    </style>
    @stack('styles')
</head>
<body>

    @include('components.admin.sidebar')

    <div class="admin-main">
        @include('components.admin.header')

        <main class="admin-content">
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @stack('scripts')
</body>
</html>
