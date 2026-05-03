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
            --header-h: 64px;
            --sidebar-w: 260px;
            --body-bg: #f1f5f9;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--body-bg);
            color: #334155;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 50;
            transition: all 0.3s;
        }
        .sidebar-brand {
            height: var(--header-h);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            letter-spacing: 0.5px;
        }
        .sidebar-brand span { color: var(--primary); margin-left: 2px; }
        
        .sidebar-nav { padding: 1.5rem 1rem; list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-nav a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar-nav a.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
        }

        /* MAIN CONTENT */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* HEADER */
        .admin-header {
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
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
            font-size: 0.9rem;
            font-weight: 600;
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }

        /* CONTENT */
        .admin-content {
            padding: 2rem;
            flex: 1;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        /* CARDS / TABLES */
        .admin-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-table th {
            text-align: left;
            padding: 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        .admin-table td {
            padding: 1rem;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background: #cbd5e1; }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .alert-success { background: var(--primary-light); color: var(--primary-dark); }
        
        /* FORMS */
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; }
        .form-control {
            width: 100%; padding: 0.6rem 1rem;
            border: 1px solid #cbd5e1; border-radius: 6px;
            font-family: inherit; font-size: 0.9rem;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    </style>
    @stack('styles')
</head>
<body>

    @include('components.admin.sidebar')

    <div class="admin-main">
        @include('components.admin.header')

        <main class="admin-content">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @stack('scripts')
</body>
</html>
