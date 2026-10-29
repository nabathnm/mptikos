@props(['header' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EasyKos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex min-h-full flex-col antialiased">
    <nav class="nb-nav sticky top-0 z-50" x-data="{ open: false }">
        <div class="nb-shell py-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-2xl font-black tracking-tight md:text-3xl">
                        <span aria-hidden="true">▦</span> EasyKos
                    </a>
                    <button type="button"
                            class="nb-btn nb-nav-toggle py-2 px-3 text-base"
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            aria-controls="main-nav">
                        Menu
                    </button>
                </div>
                <div id="main-nav"
                     class="nb-nav-menu mt-2"
                     :class="open ? 'is-open' : ''">
                    <a href="{{ route('home') }}"
                       class="nb-nav-link {{ request()->routeIs('home', 'kosan.*') ? 'is-active' : '' }}">
                        Beranda
                    </a>

                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                                Admin
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                                Users
                            </a>
                            <a href="{{ route('admin.kosan.index') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.kosan.*') ? 'is-active' : '' }}">
                                Kosan
                            </a>
                            <a href="{{ route('admin.pemesanan.index') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.pemesanan.*') ? 'is-active' : '' }}">
                                Pemesanan
                            </a>
                            <a href="{{ route('admin.ulasan.index') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.ulasan.*') ? 'is-active' : '' }}">
                                Ulasan
                            </a>
                            <a href="{{ route('admin.fasilitas.index') }}"
                               class="nb-nav-link {{ request()->routeIs('admin.fasilitas.*') ? 'is-active' : '' }}">
                                Fasilitas
                            </a>
                        @elseif(Auth::user()->role === 'pemilik')
                            <a href="{{ route('pemilik.dashboard') }}"
                               class="nb-nav-link {{ request()->routeIs('pemilik.dashboard') ? 'is-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('pemilik.kosan.index') }}"
                               class="nb-nav-link {{ request()->routeIs('pemilik.kosan.*') ? 'is-active' : '' }}">
                                Kosan Saya
                            </a>
                            <a href="{{ route('pemilik.pemesanan.index') }}"
                               class="nb-nav-link {{ request()->routeIs('pemilik.pemesanan.*') ? 'is-active' : '' }}">
                                Pemesanan
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}"
                               class="nb-nav-link {{ request()->routeIs('user.*') ? 'is-active' : '' }}">
                                Pesanan Saya
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}"
                           class="nb-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                            Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nb-btn nb-btn-danger py-2 px-3 text-base">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nb-nav-link {{ request()->routeIs('login') ? 'is-active' : '' }}">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="nb-btn nb-btn-primary py-2 px-3 text-base">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(isset($header))
        <header class="bg-white py-4" style="border-bottom: 3px solid #111827;">
            <div class="nb-shell">
                {{ $header }}
            </div>
        </header>
    @endif

    @if(session('success'))
        <div class="nb-shell mt-4">
            <div class="nb-flash nb-flash-success">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="nb-shell mt-4">
            <div class="nb-flash nb-flash-error">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1 pb-10">
        {{ $slot }}
    </main>

    <footer class="border-t-4 border-black bg-white py-10">
        <div class="nb-shell">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <div>
                    <a href="{{ url('/') }}" class="text-3xl font-black tracking-tight">
                        ▦ EasyKos
                    </a>
                    <p class="mt-2 text-lg font-medium text-gray-600">
                        Cari kosan nyaman tanpa drama.
                    </p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-lg font-black underline decoration-pink-500 decoration-4">
                        &copy; {{ date('Y') }} EasyKos Project.
                    </p>
                    <p class="mt-1 font-medium text-gray-500">
                        Malang, Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
