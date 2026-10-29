<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.users.index') }}" class="nb-btn py-1 px-2 text-base">Kembali</a>
            <span class="nb-kicker">/</span>
            <h2 class="text-3xl font-black leading-none">Tambah User</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell max-w-2xl">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <section class="nb-card p-5 md:p-6 space-y-4">
                    <div>
                        <label class="nb-label">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="nb-input">
                        @error('nama')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="nb-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="nb-input">
                        @error('email')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="nb-label">Role</label>
                        <select name="role" class="nb-select bg-white">
                            <option value="">Pilih role</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="pemilik" @selected(old('role') === 'pemilik')>Pemilik</option>
                            <option value="user" @selected(old('role') === 'user')>User</option>
                        </select>
                        @error('role')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="nb-label">No. Telepon</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" class="nb-input">
                        @error('no_telepon')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="nb-label">Password</label>
                        <input type="password" name="password" class="nb-input">
                        @error('password')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="nb-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="nb-input">
                    </div>
                </section>

                <div class="flex flex-col gap-3 md:flex-row">
                    <a href="{{ route('admin.users.index') }}" class="nb-btn w-full md:flex-1">Batal</a>
                    <button type="submit" class="nb-btn nb-btn-primary w-full md:flex-1">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
