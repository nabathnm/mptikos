<x-app-layout>
    <x-slot name="header">
        <p class="nb-kicker">Akun</p>
        <h2 class="text-3xl font-black leading-none md:text-4xl">Profil Saya</h2>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @if($user->role === 'pemilik')
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['total_kosan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Kosan</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['kamar_tersedia'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Kamar Tersedia</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['total_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Pemesanan</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['pending_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Pending</p>
                    </article>
                @else
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['total_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Total Pesanan</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['pending_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Pending</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['disetujui_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Disetujui</p>
                    </article>
                    <article class="nb-card p-4 text-center">
                        <p class="text-3xl font-black md:text-4xl">{{ $stats['ditolak_pemesanan'] ?? 0 }}</p>
                        <p class="nb-kicker mt-1">Ditolak</p>
                    </article>
                @endif
            </section>

            <section class="nb-card p-6 md:p-8">
                <h3 class="text-3xl font-black leading-none">Data Profil</h3>

                @if(session('status') === 'profile-updated')
                    <p class="nb-flash nb-flash-success mt-4">Profil berhasil diperbarui.</p>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="nb-label">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="nb-input">
                        @error('nama')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nb-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="nb-input">
                        @error('email')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nb-label">No. Telepon</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="nb-input" placeholder="08xxxxxxxxxx">
                        @error('no_telepon')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <p class="nb-kicker">
                        Role:
                        <span class="font-black capitalize">{{ $user->role === 'pemilik' ? 'Pemilik Kos' : 'Pencari Kos' }}</span>
                    </p>

                    <button type="submit" class="nb-btn nb-btn-primary">Simpan Perubahan</button>
                </form>
            </section>

            <section class="nb-card p-6 md:p-8">
                <h3 class="text-3xl font-black leading-none">Ganti Password</h3>

                @if(session('status') === 'password-updated')
                    <p class="nb-flash nb-flash-success mt-4">Password berhasil diubah.</p>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label class="nb-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="nb-input" required>
                        @error('current_password', 'updatePassword')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nb-label">Password Baru</label>
                        <input type="password" name="password" class="nb-input" required>
                        @error('password', 'updatePassword')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nb-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="nb-input" required>
                        @error('password_confirmation', 'updatePassword')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="nb-btn nb-btn-secondary">Update Password</button>
                </form>
            </section>

            <section class="nb-card p-6 md:p-8">
                <h3 class="text-3xl font-black leading-none">Zona Bahaya</h3>
                <p class="mt-2 text-lg font-medium">Hapus akun akan menghapus data login Anda secara permanen.</p>

                <form method="POST" action="{{ route('profile.destroy') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('delete')

                    <div>
                        <label class="nb-label">Konfirmasi Password</label>
                        <input type="password" name="password" class="nb-input" required>
                        @error('password', 'userDeletion')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="nb-btn nb-btn-danger" onclick="return confirm('Yakin ingin menghapus akun ini?')">
                        Hapus Akun
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
