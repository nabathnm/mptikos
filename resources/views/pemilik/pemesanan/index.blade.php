<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <span class="nb-kicker">Pemilik</span>
            <span class="nb-kicker">/</span>
            <h2 class="text-4xl font-black leading-none">Pemesanan Masuk</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card p-5 md:p-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="nb-kicker">Daftar pengajuan masuk</p>
                        <h3 class="mt-2 text-3xl font-black leading-none">Kelola pesanan satu per satu</h3>
                    </div>
                    <p class="nb-kicker max-w-xl md:text-right">
                        Cek status, buka detail, lalu setujui atau tolak pengajuan dari calon penyewa.
                    </p>
                </div>
            </section>

            @if($pemesanans->count())
                <div class="space-y-4">
                    @foreach($pemesanans as $pemesanan)
                        @php
                            $statusConfig = [
                                'pending' => ['label' => 'Menunggu', 'tone' => 'bg-yellow-200'],
                                'disetujui' => ['label' => 'Disetujui', 'tone' => 'bg-green-200'],
                                'ditolak' => ['label' => 'Ditolak', 'tone' => 'bg-red-200'],
                                'dibatalkan' => ['label' => 'Dibatalkan', 'tone' => 'bg-gray-200'],
                            ];
                            $sc = $statusConfig[$pemesanan->status] ?? $statusConfig['pending'];
                        @endphp

                        <article class="nb-card p-4 md:p-5">
                            <div class="grid gap-4 md:grid-cols-[160px_minmax(0,1fr)_auto] md:items-start">
                                <div class="h-36 overflow-hidden border-2 border-black bg-gray-200">
                                    @if($pemesanan->kosan && $pemesanan->kosan->fotoUtama)
                                        <img src="{{ asset('storage/' . $pemesanan->kosan->fotoUtama->foto) }}"
                                             alt="{{ $pemesanan->kosan->nama_kosan }}"
                                             class="h-full w-full object-cover">
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-start gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-3xl font-black leading-none">{{ $pemesanan->kosan->nama_kosan ?? '-' }}</p>
                                            <p class="mt-2 text-lg font-medium">
                                                {{ $pemesanan->user->nama ?? '-' }}
                                                <span class="nb-kicker">/</span>
                                                {{ $pemesanan->user->email ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="nb-kicker border-2 border-black px-2 py-1 {{ $sc['tone'] }}">
                                            {{ $sc['label'] }}
                                        </span>
                                    </div>

                                    <div class="mt-4 grid gap-2 text-lg font-medium md:grid-cols-2">
                                        <div class="nb-card-soft px-3 py-2">
                                            <span class="nb-kicker block">Tanggal Masuk</span>
                                            <span class="font-black">{{ \Carbon\Carbon::parse($pemesanan->tanggal_masuk)->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="nb-card-soft px-3 py-2">
                                            <span class="nb-kicker block">Durasi</span>
                                            <span class="font-black">{{ $pemesanan->durasi_bulan }} bulan</span>
                                        </div>
                                        <div class="nb-card-soft px-3 py-2 md:col-span-2">
                                            <span class="nb-kicker block">Total Biaya</span>
                                            <span class="font-black">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex min-w-[180px] flex-col gap-2 md:items-stretch">
                                    <a href="{{ route('pemilik.pemesanan.show', $pemesanan) }}" class="nb-btn w-full py-2 text-base">Detail</a>
                                    @if($pemesanan->status === 'pending')
                                        <form method="POST" action="{{ route('pemilik.pemesanan.setujui', $pemesanan) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="nb-btn nb-btn-primary w-full py-2 text-base">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('pemilik.pemesanan.tolak', $pemesanan) }}" onsubmit="return confirm('Yakin ingin menolak pemesanan ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="nb-btn nb-btn-danger w-full py-2 text-base">Tolak</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="nb-card mt-6 p-4">
                    {{ $pemesanans->links() }}
                </div>
            @else
                <section class="nb-card p-10 text-center">
                    <p class="text-4xl font-black leading-none">Belum Ada Pemesanan</p>
                    <p class="mt-3 text-lg font-medium">Belum ada pengajuan untuk kosan Anda.</p>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
