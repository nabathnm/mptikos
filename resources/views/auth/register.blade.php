<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — EasyKos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <div class="nb-shell min-h-screen flex items-center py-10">
        <section class="nb-card mx-auto w-full max-w-2xl p-6 md:p-8">
            <a href="{{ url('/') }}" class="nb-kicker hover:underline">Kembali ke Landing</a>
            <h1 class="mt-2 text-5xl font-black leading-none">Daftar Akun</h1>
            <p class="mt-2 text-xl font-semibold">Pilih role, isi data inti, langsung pakai.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4">
                @csrf

                <div>
                    <label class="nb-label">Daftar sebagai</label>
                    <select name="role" required
                            class="nb-select">
                        <option value="user" @selected(old('role', request('role')) === 'user')>Pencari Kos</option>
                        <option value="pemilik" @selected(old('role', request('role')) === 'pemilik')>Pemilik Kos</option>
                    </select>
                    @error('role')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="nb-label">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="nb-input" placeholder="Nama kamu">
                    @error('nama')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="nb-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="nb-input" placeholder="nama@email.com">
                    @error('email')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="nb-label">Password</label>
                    <input type="password" name="password" required
                           class="nb-input" placeholder="Minimal 8 karakter">
                    @error('password')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="nb-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="nb-input" placeholder="Ulangi password">
                </div>

                <button type="submit" class="nb-btn nb-btn-primary w-full text-xl mt-2">
                    Daftar
                </button>
            </form>

            <p class="mt-6 text-lg font-medium">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-black underline">Masuk</a>
            </p>
        </section>
    </div>
</body>
</html>
