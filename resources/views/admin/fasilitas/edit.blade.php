<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.fasilitas.index') }}" class="nb-btn py-1 px-2 text-base">Kembali</a>
            <span class="nb-kicker">/</span>
            <h2 class="text-3xl font-black leading-none">Edit Fasilitas</h2>
            <span class="nb-kicker">{{ $fasilitas->nama_fasilitas }}</span>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell max-w-xl">
            <form method="POST" action="{{ route('admin.fasilitas.update', $fasilitas) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="nb-card p-5 md:p-6 space-y-4">
                    <div>
                        <label class="nb-label">Nama Fasilitas</label>
                        <input type="text" name="nama_fasilitas" value="{{ old('nama_fasilitas', $fasilitas->nama_fasilitas) }}" class="nb-input">
                        @error('nama_fasilitas')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                </section>

                <div class="flex flex-col gap-3 md:flex-row">
                    <a href="{{ route('admin.fasilitas.index') }}" class="nb-btn w-full md:flex-1">Batal</a>
                    <button type="submit" class="nb-btn nb-btn-primary w-full md:flex-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
