<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kosan.show', $kosan->id) }}" class="nb-btn py-1 px-2 text-base">{{ $kosan->nama_kosan }}</a>
            <span class="nb-kicker">/</span>
            <span class="text-3xl font-black leading-none">Ajukan Pemesanan</span>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell max-w-3xl space-y-6">
            @if(session('error'))
                <div class="nb-flash nb-flash-error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('user.pemesanan.store') }}" enctype="multipart/form-data" x-data="{
                harga: {{ $kosan->harga_per_bulan }},
                durasi: {{ old('durasi_bulan', 1) }},
                get totalHarga() { return this.harga * this.durasi; }
            }" class="space-y-6">
                @csrf
                <input type="hidden" name="kosan_id" value="{{ $kosan->id }}">

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Kosan Dipilih</h3>
                    <div class="mt-5 flex flex-col gap-4 md:flex-row">
                        <div class="h-28 w-full shrink-0 overflow-hidden border-2 border-black bg-gray-200 md:w-36">
                            @if($kosan->fotoUtama)
                                <img src="{{ $kosan->fotoUtama->url }}" alt="{{ $kosan->nama_kosan }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-3xl font-black leading-none">{{ $kosan->nama_kosan }}</p>
                            <p class="mt-2 text-lg font-medium">{{ $kosan->alamat }}, {{ $kosan->kota }}</p>
                            <p class="mt-2 text-2xl font-black">Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }} <span class="nb-kicker">/ bulan</span></p>
                            <p class="nb-kicker mt-2">Pemilik: {{ $kosan->pemilik->nama ?? '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Detail Pemesanan</h3>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="nb-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="nb-input">
                            @error('tanggal_masuk')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Durasi Sewa</label>
                            <select name="durasi_bulan" x-model.number="durasi" class="nb-select bg-white">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" @selected(old('durasi_bulan', 1) == $i)>{{ $i }} Bulan</option>
                                @endfor
                            </select>
                            @error('durasi_bulan')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Catatan</label>
                            <textarea name="catatan" rows="3" class="nb-textarea" placeholder="Waktu kedatangan, pertanyaan khusus, dll.">{{ old('catatan') }}</textarea>
                            @error('catatan')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Informasi Pembayaran</h3>
                    <p class="nb-kicker mt-1">Silakan transfer sesuai total biaya sewa ke rekening pemilik kos berikut:</p>

                    <div class="nb-card-soft mt-4 p-4">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div>
                                <span class="nb-kicker">Bank / E-Wallet</span>
                                <p class="text-xl font-black">{{ $kosan->nama_bank ?? 'Hubungi Pemilik' }}</p>
                            </div>
                            <div>
                                <span class="nb-kicker">Nomor Rekening</span>
                                <p class="text-xl font-black">{{ $kosan->nomor_rekening ?? ($kosan->pemilik->no_telepon ?? '-') }}</p>
                            </div>
                            <div>
                                <span class="nb-kicker">Atas Nama</span>
                                <p class="text-xl font-black">{{ $kosan->nama_rekening ?? ($kosan->pemilik->nama ?? '-') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="nb-label">Unggah Bukti Transfer (Opsional)</label>
                        <p class="nb-kicker mb-2">Bisa diunggah sekarang atau nanti setelah pesanan dibuat (Format: JPG, PNG, maks 10MB).</p>
                        <input type="file" name="bukti_pembayaran" accept="image/*" class="nb-input bg-white">
                        @error('bukti_pembayaran')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-4 border-2 border-black bg-yellow-100 p-3 text-sm font-bold">
                        ⚡ <strong>Aturan Alokasi Kamar (First-Approved):</strong> Kamar akan diberikan kepada penyewa yang bukti transfernya disetujui lebih dahulu oleh pemilik. Jika kuota kamar habis, pesanan lain otomatis dibatalkan.
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Ringkasan Biaya</h3>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between border-b-2 border-black pb-2 text-lg font-medium">
                            <span>Harga per bulan</span>
                            <span>Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b-2 border-black pb-2 text-lg font-medium">
                            <span>Durasi</span>
                            <span x-text="durasi + ' bulan'"></span>
                        </div>
                        <div class="flex items-center justify-between text-2xl font-black">
                            <span>Total yang Harus Ditransfer</span>
                            <span x-text="'Rp ' + totalHarga.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </section>

                <div class="flex gap-3">
                    <a href="{{ route('kosan.show', $kosan->id) }}" class="nb-btn flex-1">Batal</a>
                    <button type="submit" class="nb-btn nb-btn-primary flex-1">Ajukan Pemesanan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
