<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="nb-kicker">Admin</p>
                <h2 class="text-4xl font-black leading-none">Semua Kosan</h2>
            </div>
            <a href="{{ route('admin.kosan.create') }}" class="nb-btn nb-btn-primary">Tambah Kosan</a>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell">
            @if($kosans->isEmpty())
                <section class="nb-card p-10 text-center">
                    <p class="text-4xl font-black">Belum Ada Kosan</p>
                    <p class="mt-2 text-lg font-medium">Tambahkan kosan baru sekarang.</p>
                    <a href="{{ route('admin.kosan.create') }}" class="nb-btn nb-btn-primary mt-4">Tambah Kosan</a>
                </section>
            @else
                <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($kosans as $kosan)
                        <article class="nb-card overflow-hidden">
                            <div class="h-48 bg-gray-200">
                                @if($kosan->fotoUtama)
                                    <img src="{{ asset('storage/' . $kosan->fotoUtama->foto) }}"
                                         alt="{{ $kosan->nama_kosan }}"
                                         class="h-full w-full object-cover">
                                @endif
                            </div>

                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-3xl font-black leading-none">{{ $kosan->nama_kosan }}</h3>
                                    <span class="nb-kicker border-2 border-black px-2 py-1 {{ $kosan->status === 'aktif' ? 'bg-green-200' : 'bg-gray-200' }}">
                                        {{ ucfirst($kosan->status) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-lg font-medium">{{ $kosan->kota }}</p>
                                <p class="nb-kicker mt-1">Pemilik: {{ $kosan->pemilik->nama ?? '-' }}</p>
                                <p class="mt-2 text-2xl font-black">Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }}</p>
                                <p class="nb-kicker mt-1">{{ $kosan->kamar_tersedia }}/{{ $kosan->jumlah_kamar }} kamar</p>

                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <a href="{{ route('admin.kosan.show', $kosan) }}" class="nb-btn py-2 text-base">Detail</a>
                                    <a href="{{ route('admin.kosan.edit', $kosan) }}" class="nb-btn nb-btn-secondary py-2 text-base">Edit</a>
                                    <form action="{{ route('admin.kosan.destroy', $kosan) }}" method="POST" onsubmit="return confirm('Hapus kosan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="nb-btn nb-btn-danger w-full py-2 text-base">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="nb-card mt-6 p-4">
                    {{ $kosans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
