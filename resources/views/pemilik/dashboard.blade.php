<x-app-layout>
    <x-slot name="header">
        <p class="nb-kicker">Pemilik</p>
        <h2 class="text-3xl font-black leading-none md:text-4xl">Dashboard</h2>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card p-5 md:p-6">
                <p class="nb-kicker">Ringkasan</p>
                <h3 class="mt-1 text-3xl font-black">Halo, {{ Auth::user()->nama }}.</h3>
                <p class="mt-1 text-lg font-medium">Kelola kosan dan pemesanan dari satu halaman.</p>
                <a href="{{ route('pemilik.kosan.create') }}" class="nb-btn nb-btn-primary mt-4">Tambah Kosan</a>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="nb-card p-4">
                    <p class="nb-kicker">Total</p>
                    <p class="mt-2 text-3xl font-black md:text-4xl">{{ $totalKosan ?? 0 }}</p>
                    <p class="text-lg font-medium">Kosan</p>
                </article>
                <article class="nb-card p-4">
                    <p class="nb-kicker">Total</p>
                    <p class="mt-2 text-3xl font-black md:text-4xl">{{ $totalPemesanan ?? 0 }}</p>
                    <p class="text-lg font-medium">Pemesanan</p>
                </article>
                <article class="nb-card p-4">
                    <p class="nb-kicker">Menunggu</p>
                    <p class="mt-2 text-3xl font-black md:text-4xl">{{ $pemesananPending ?? 0 }}</p>
                    <p class="text-lg font-medium">Konfirmasi</p>
                </article>
                <article class="nb-card p-4">
                    <p class="nb-kicker">Kamar</p>
                    <p class="mt-2 text-3xl font-black md:text-4xl">{{ $totalKamarTersedia ?? 0 }}</p>
                    <p class="text-lg font-medium">Tersedia</p>
                </article>
            </section>

            <section class="nb-card p-5 md:p-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <h3 class="text-2xl font-black leading-none md:text-3xl">Kosan Terbaru</h3>
                    <a href="{{ route('pemilik.kosan.index') }}" class="nb-btn">Lihat Semua</a>
                </div>

                @if(isset($kosanTerbaru) && $kosanTerbaru->count())
                    <div class="mt-4 space-y-3">
                        @foreach($kosanTerbaru as $kosan)
                            <a href="{{ route('pemilik.kosan.show', $kosan) }}" class="nb-card-soft nb-card-hover block p-3">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <div class="h-44 w-full shrink-0 overflow-hidden border-2 border-black bg-gray-200 sm:h-16 sm:w-20">
                                        @if($kosan->fotoUtama)
                                            <img src="{{ asset('storage/' . $kosan->fotoUtama->foto) }}"
                                                 alt="{{ $kosan->nama_kosan }}"
                                                 class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-2xl font-black leading-none">{{ $kosan->nama_kosan }}</p>
                                        <p class="mt-1 text-lg font-medium">{{ $kosan->kota }}</p>
                                        <p class="nb-kicker mt-1">Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }} / bulan</p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <span class="nb-kicker border-2 border-black px-2 py-1 {{ $kosan->status === 'aktif' ? 'bg-green-200' : 'bg-gray-200' }}">
                                            {{ ucfirst($kosan->status) }}
                                        </span>
                                        <p class="nb-kicker mt-2">{{ $kosan->kamar_tersedia }}/{{ $kosan->jumlah_kamar }} kamar</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="nb-card-soft mt-4 p-8 text-center">
                        <p class="text-3xl font-black">Belum ada kosan.</p>
                        <p class="mt-2 text-lg font-medium">Tambah kosan pertama untuk mulai menerima pemesanan.</p>
                        <a href="{{ route('pemilik.kosan.create') }}" class="nb-btn nb-btn-primary mt-4">Tambah Kosan</a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
