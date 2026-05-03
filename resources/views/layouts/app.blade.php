<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name','Hotel SaaS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles (copied minimal Tailwind-like styles from welcome) -->
        <style>
            /* minimal utility styles (kept from welcome view for parity) */
            /* trimmed for brevity - full styles exist in welcome if needed */
            body{font-family:Figtree,ui-sans-serif,system-ui,sans-serif;margin:0}
            .font-sans{font-family:Figtree,ui-sans-serif,system-ui,sans-serif}
            .max-w-7xl{max-width:80rem;margin-left:auto;margin-right:auto}
            .px-6{padding-left:1.5rem;padding-right:1.5rem}
            .py-4{padding-top:1rem;padding-bottom:1rem}
            .flex{display:flex}.items-center{align-items:center}.justify-between{justify-content:space-between}
            .text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}
            .bg-white{background:#fff}.bg-gray-50{background:#f9fafb}
            .rounded{border-radius:.25rem}.rounded-lg{border-radius:.5rem}
            a{color:inherit;text-decoration:none}
        </style>
    </head>
    <body class="font-sans bg-gray-50 text-black">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="/" class="text-lg font-semibold">{{ config('app.name','Hotel SaaS') }}</a>
                    <nav class="hidden sm:flex gap-3 text-sm">
                        <a href="/">Home</a>
                        <a href="/#about">About</a>
                        <a href="/#hotels">Hotels</a>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="mt-12 bg-white py-6">
            <div class="max-w-7xl px-6 text-sm text-center text-black/60">&copy; {{ date('Y') }} {{ config('app.name','Hotel SaaS') }} — Built with Laravel</div>
        </footer>
    </body>
</html>
