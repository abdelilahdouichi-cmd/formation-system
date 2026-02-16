<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin · '.config('app.name'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div class="relative min-h-screen overflow-hidden lg:flex">
            <div class="pointer-events-none absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-sky-200/60 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-orange-200/50 blur-3xl"></div>

            <aside class="border-b border-slate-200/70 bg-white/90 backdrop-blur lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
                <div class="flex items-center gap-3 px-5 py-4 text-lg font-semibold tracking-tight">
                    <div class="h-9 w-9 rounded-xl bg-slate-900 text-white"></div>
                    Admin Panel
                </div>
                <nav class="px-3 pb-6">
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
                       href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                    <a class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
                       href="{{ route('admin.users.index') }}">
                        Utilisateurs
                    </a>
                    <div class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-400">
                        Rôles & Permissions
                    </div>
                    <div class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-400">
                        Paramètres
                    </div>
                </nav>
            </aside>

            <div class="flex-1">
                <header class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/70 bg-white/90 px-6 py-4 backdrop-blur">
                    <div class="text-sm font-semibold text-slate-700">@yield('header', 'Admin Dashboard')</div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-sm text-slate-500 sm:block">{{ auth()->user()->email }}</div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white">
                            {{ str(auth()->user()->name)->substr(0, 2)->upper() }}
                        </div>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-full border border-slate-200 px-4 py-1.5 text-sm text-slate-700 transition hover:bg-slate-100" type="submit">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </header>

                @if (session('status'))
                    <div class="px-6 pt-4">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <main class="px-6 py-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
