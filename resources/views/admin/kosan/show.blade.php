<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.kosan.index') }}" class="nb-btn py-1 px-2 text-base">Semua Kosan</a>
            <span class="nb-kicker">/</span>
            <span class="text-3xl font-black leading-none">Detail Kosan</span>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card overflow-hidden">
                @if($kosan->fotos->count())
                    @php $fotoUtama = $kosan->fotos->firstWhere('is_utama', true) ?? $kosan->fotos->first(); @endphp
                    <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_320px]">
                        <div class="bg-gray-200">
                            <img src="{{ asset('storage/' . $fotoUtama->foto) }}" alt="{{ $kosan->nama_kosan }}" class="h-72 w-full object-cover md:h-[420px]">
                        </div>
                        @if($kosan->fotos->count() > 1)
                            <div class="border-t-2 border-black lg:border-t-0 lg:border-l-2">
                                <div class="grid grid-cols-3 gap-2 p-3 sm:grid-cols-4 lg:grid-cols-2">
                                    @foreach($kosan->fotos as $foto)
                                        <img src="{{ asset('storage/' . $foto->foto) }}" alt="Foto {{ $loop->iteration }}" class="h-20 w-full border-2 border-black object-cover">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex h-72 items-center justify-center bg-gray-200 px-4 text-center md:h-[420px]">
                        <p class="text-3xl font-black">Belum ada foto</p>
                    </div>
                @endif
            </section>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-6">
                    <section class="nb-card p-5 md:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="nb-kicker">Informasi</p>
                                <h1 class="text-3xl font-black leading-none md:text-4xl">{{ $kosan->nama_kosan }}</h1>
                                <p class="mt-2 text-lg font-medium">{{ $kosan->alamat }}, {{ $kosan->kota }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="nb-kicker border-2 border-black px-2 py-1 {{ $kosan->status === 'aktif' ? 'bg-green-200' : 'bg-gray-200' }}">
                                    {{ ucfirst($kosan->status) }}
                                </span>
                                <span class="nb-kicker border-2 border-black px-2 py-1 {{ $kosan->tipe === 'putra' ? 'bg-blue-200' : ($kosan->tipe === 'putri' ? 'bg-pink-200' : 'bg-yellow-200') }}">
                                    {{ ucfirst($kosan->tipe) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="nb-card-soft p-4 text-center">
                                <p class="text-3xl font-black md:text-4xl">Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }}</p>
                                <p class="nb-kicker mt-1">per bulan</p>
                            </div>
                            <div class="nb-card-soft p-4 text-center">
                                <p class="text-3xl font-black md:text-4xl">{{ $kosan->kamar_tersedia }}/{{ $kosan->jumlah_kamar }}</p>
                                <p class="nb-kicker mt-1">kamar</p>
                            </div>
                            <div class="nb-card-soft p-4 text-center">
                                <p class="text-3xl font-black md:text-4xl">{{ $kosan->fotos->count() }}</p>
                                <p class="nb-kicker mt-1">foto</p>
                            </div>
                        </div>
                    </section>

                    <section class="nb-card p-5 md:p-6">
                        <h2 class="text-3xl font-black leading-none">Deskripsi</h2>
                        <p class="mt-3 whitespace-pre-line text-lg font-medium">{{ $kosan->deskripsi }}</p>
                    </section>

                    @if(!empty($kosan->fasilitas))
                        <section class="nb-card p-5 md:p-6">
                            <h2 class="text-3xl font-black leading-none">Fasilitas</h2>
                            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                                @foreach($kosan->fasilitas as $f)
                                    <div class="nb-card-soft px-3 py-2 text-lg font-semibold">{{ $f }}</div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-6">
                    <section class="nb-card p-5 md:p-6">
                        <h3 class="text-2xl font-black leading-none">Pemilik</h3>
                        <p class="mt-2 text-xl font-semibold">{{ $kosan->pemilik->nama ?? '-' }}</p>
                        <p class="nb-kicker mt-1">{{ $kosan->pemilik->email ?? '-' }}</p>
                        @if($kosan->pemilik->no_telepon)
                            <p class="nb-kicker mt-1">{{ $kosan->pemilik->no_telepon }}</p>
                        @endif
                    </section>

                    <section class="nb-card p-5 md:p-6">
                        <h3 class="text-2xl font-black leading-none">Aksi</h3>
                        <div class="mt-4 flex flex-col gap-3">
                            <a href="{{ route('admin.kosan.edit', $kosan) }}" class="nb-btn nb-btn-secondary w-full">Edit</a>
                            <form action="{{ route('admin.kosan.destroy', $kosan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kosan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="nb-btn nb-btn-danger w-full">Hapus</button>
                            </form>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
