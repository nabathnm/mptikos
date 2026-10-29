<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyKos | Selamat Datang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <div class="nb-shell min-h-screen flex items-center py-10">
        <section class="nb-card w-full p-6 md:p-10">
            <p class="nb-kicker mb-3">Platform Kosan</p>
            <h1 class="text-5xl leading-none font-black md:text-7xl">EasyKos</h1>
            <p class="mt-4 max-w-xl text-2xl font-semibold leading-tight">
                Cari dan kelola kosan secara cepat dan langsung.
                Fokus ke info kamar, harga, dan keputusan cepat.
            </p>

            @auth
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    @if(Auth::user()->role === 'pemilik')
                        <a href="{{ route('pemilik.dashboard') }}" class="nb-card nb-card-hover p-5">
                            <p class="nb-kicker">Akun Anda</p>
                            <p class="mt-2 text-3xl font-black leading-none">Dashboard Pemilik</p>
                            <p class="mt-2 text-lg font-medium">Kelola kosan dan pemesanan masuk.</p>
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="nb-card nb-card-hover p-5">
                            <p class="nb-kicker">Akun Anda</p>
                            <p class="mt-2 text-3xl font-black leading-none">Pesanan Saya</p>
                            <p class="mt-2 text-lg font-medium">Lihat progres dan detail pemesanan.</p>
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="nb-card-soft nb-card-hover p-5">
                        <p class="nb-kicker">Jelajah</p>
                        <p class="mt-2 text-3xl font-black leading-none">Cari Kos</p>
                        <p class="mt-2 text-lg font-medium">Lihat daftar kosan yang tersedia.</p>
                    </a>
                </div>

                <div class="mt-8">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nb-btn nb-btn-danger">Keluar</button>
                    </form>
                </div>
            @else
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <a href="{{ route('home') }}" class="nb-card-soft nb-card-hover p-5">
                        <p class="nb-kicker">Akses Umum</p>
                        <p class="mt-2 text-3xl font-black leading-none">Cari Kos</p>
                        <p class="mt-2 text-lg font-medium">Lihat listing tanpa login.</p>
                    </a>
                    <a href="{{ route('login') }}" class="nb-card nb-card-hover p-5" style="background: var(--nb-secondary);">
                        <p class="nb-kicker">Akses Akun</p>
                        <p class="mt-2 text-3xl font-black leading-none">Masuk Akun</p>
                        <p class="mt-2 text-lg font-medium">Lanjutkan kelola properti dan pesanan.</p>
                    </a>
                </div>

                <div class="mt-8 grid gap-3 md:grid-cols-2">
                    <a href="{{ route('register', ['role' => 'user']) }}" class="nb-btn nb-btn-primary">
                        Daftar sebagai Pencari Kos
                    </a>
                    <a href="{{ route('register', ['role' => 'pemilik']) }}" class="nb-btn nb-btn-secondary">
                        Daftar sebagai Pemilik Kos
                    </a>
                </div>
            @endauth
        </section>
    </div>
</body>
</html>
