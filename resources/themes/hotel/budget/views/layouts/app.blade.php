<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Budget Theme')</title>
    <link rel="stylesheet" href="{{ asset('assets/themes/hotel/budget/css/app.css') }}">
    @stack('head')
</head>
<body>
    @include('themes.hotel.budget.views.layouts.header')
    <main class="container">
        @yield('content')
    </main>
    @include('themes.hotel.budget.views.layouts.footer')
    <script src="{{ asset('assets/themes/hotel/budget/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
