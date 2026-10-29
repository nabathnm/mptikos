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

            <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', request('role', 'user')) }}' }" class="mt-7 space-y-4">
                @csrf

                <div>
                    <label class="nb-label">Daftar sebagai</label>
                    <select name="role" x-model="role" required class="nb-select">
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
                    <label class="nb-label">Nomor WhatsApp / Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                           class="nb-input" placeholder="Contoh: 08123456789">
                    @error('no_telepon')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <!-- Bagian Khusus Pemilik Kos: Rekening Pembayaran -->
                <div x-show="role === 'pemilik'" x-transition class="border-2 border-black bg-yellow-50 p-4 space-y-4">
                    <div>
                        <p class="text-xl font-black">Informasi Rekening Pembayaran</p>
                        <p class="nb-kicker mt-1">Rekening ini otomatis digunakan untuk semua kosan Anda agar penyewa bisa transfer biaya sewa.</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="nb-label">Nama Bank / E-Wallet</label>
                            <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                   placeholder="Contoh: BCA / Mandiri / GoPay" class="nb-input bg-white">
                            @error('nama_bank')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening') }}"
                                   placeholder="Contoh: 1234567890" class="nb-input bg-white">
                            @error('nomor_rekening')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="nb-label">Atas Nama Pemilik Rekening</label>
                            <input type="text" name="nama_rekening" value="{{ old('nama_rekening') }}"
                                   placeholder="Contoh: Budi Santoso (kosongkan jika sama dengan nama akun)" class="nb-input bg-white">
                            @error('nama_rekening')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
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
