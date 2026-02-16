<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900">
        <div class="relative min-h-screen overflow-hidden bg-slate-50">
            <div class="pointer-events-none absolute -left-32 top-0 h-72 w-72 rounded-full bg-sky-200/50 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-24 top-32 h-64 w-64 rounded-full bg-orange-200/50 blur-3xl"></div>

            <nav class="border-b border-slate-200/60 bg-white/80 backdrop-blur">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                    <a class="text-lg font-semibold tracking-tight text-slate-900" href="{{ route('dashboard') }}">
                        {{ config('app.name') }}
                    </a>
                    <div class="flex items-center gap-4 text-sm text-slate-600">
                        @auth
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button class="rounded-full border border-slate-200 px-4 py-1.5 text-slate-700 transition hover:bg-slate-100" type="submit">
                                    Déconnexion
                                </button>
                            </form>
                        @endauth
                        @guest
                            <a class="rounded-full border border-slate-200 px-4 py-1.5 text-slate-700 transition hover:bg-slate-100" href="{{ route('login') }}">
                                Connexion
                            </a>
                        @endguest
                    </div>
                </div>
            </nav>

            @if (session('status'))
                <div class="mx-auto mt-4 max-w-6xl px-4">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <main class="mx-auto max-w-6xl px-4 py-8">
                @yield('content')
            </main>
        </div>
    </body>
</html>
