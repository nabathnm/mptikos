<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <span class="nb-kicker">Admin</span>
            <span class="nb-kicker">/</span>
            <h2 class="text-4xl font-black leading-none">Dashboard</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="nb-card p-4">
                    <p class="nb-kicker">Total Users</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_users'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Pemilik</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_pemilik'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Pencari</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_user'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Kosan</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_kosan'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Pemesanan</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_pemesanan'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Pending</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['pending_pemesanan'] }}</p>
                </div>
                <div class="nb-card p-4">
                    <p class="nb-kicker">Ulasan</p>
                    <p class="mt-2 text-3xl font-black">{{ $stats['total_ulasan'] }}</p>
                </div>
            </section>

            <section class="nb-card p-5 md:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="nb-kicker">Terbaru</p>
                        <h3 class="text-3xl font-black leading-none">Kosan Terakhir</h3>
                    </div>
                    <a href="{{ route('admin.kosan.index') }}" class="nb-btn nb-btn-secondary">Kelola Kosan</a>
                </div>
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($kosanTerbaru as $kosan)
                        <article class="nb-card-soft overflow-hidden">
                            <div class="h-36 bg-gray-200">
                                @if($kosan->fotoUtama)
                                    <img src="{{ asset('storage/' . $kosan->fotoUtama->foto) }}" alt="{{ $kosan->nama_kosan }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-2xl font-black leading-none">{{ $kosan->nama_kosan }}</p>
                                <p class="mt-1 text-lg font-medium">{{ $kosan->kota }}</p>
                                <p class="nb-kicker mt-1">Pemilik: {{ $kosan->pemilik->nama ?? '-' }}</p>
                            </div>
                        </article>
                    @empty
                        <div class="nb-card-soft p-6 text-center">
                            <p class="text-xl font-black">Belum ada kosan</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="nb-card p-5 md:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="nb-kicker">Terbaru</p>
                        <h3 class="text-3xl font-black leading-none">Pemesanan Terakhir</h3>
                    </div>
                    <a href="{{ route('admin.pemesanan.index') }}" class="nb-btn nb-btn-secondary">Kelola Pemesanan</a>
                </div>
                <div class="mt-5 space-y-3">
                    @php
                        $statusConfig = [
                            'pending' => ['label' => 'Menunggu', 'tone' => 'bg-yellow-200'],
                            'disetujui' => ['label' => 'Disetujui', 'tone' => 'bg-green-200'],
                            'ditolak' => ['label' => 'Ditolak', 'tone' => 'bg-red-200'],
                            'dibatalkan' => ['label' => 'Dibatalkan', 'tone' => 'bg-gray-200'],
                        ];
                    @endphp
                    @forelse($pemesananTerbaru as $pemesanan)
                        @php $sc = $statusConfig[$pemesanan->status] ?? $statusConfig['pending']; @endphp
                        <div class="nb-card-soft flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <div>
                                <p class="text-xl font-black leading-none">{{ $pemesanan->kosan->nama_kosan ?? '-' }}</p>
                                <p class="nb-kicker mt-1">{{ $pemesanan->user->nama ?? '-' }} / {{ $pemesanan->kosan->pemilik->nama ?? '-' }}</p>
                            </div>
                            <span class="nb-kicker border-2 border-black px-2 py-1 {{ $sc['tone'] }}">{{ $sc['label'] }}</span>
                        </div>
                    @empty
                        <div class="nb-card-soft p-6 text-center">
                            <p class="text-xl font-black">Belum ada pemesanan</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
