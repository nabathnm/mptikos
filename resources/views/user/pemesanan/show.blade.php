<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('user.dashboard') }}" class="nb-btn py-1 px-2 text-base">Pesanan Saya</a>
            <span class="nb-kicker">/</span>
            <span class="text-3xl font-black leading-none">Detail Pemesanan</span>
            <span class="nb-kicker">#{{ str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell max-w-3xl space-y-6">
            @php
                $statusConfig = [
                    'pending' => ['label' => 'Menunggu Persetujuan', 'tone' => 'bg-yellow-200'],
                    'disetujui' => ['label' => 'Disetujui', 'tone' => 'bg-green-200'],
                    'ditolak' => ['label' => 'Ditolak', 'tone' => 'bg-red-200'],
                    'dibatalkan' => ['label' => 'Dibatalkan', 'tone' => 'bg-gray-200'],
                ];
                $sc = $statusConfig[$pemesanan->status] ?? $statusConfig['pending'];
            @endphp

            <section class="nb-card p-5 md:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-4xl font-black leading-none">{{ $sc['label'] }}</p>
                        <p class="nb-kicker mt-1">
                            @if($pemesanan->status === 'pending')
                                Menunggu konfirmasi dan persetujuan dari pemilik kosan.
                            @elseif($pemesanan->status === 'disetujui')
                                Pesanan Anda telah disetujui oleh pemilik kos!
                            @elseif($pemesanan->status === 'ditolak')
                                Pesanan ditolak oleh pemilik kosan.
                            @else
                                Pesanan ini sudah dibatalkan.
                            @endif
                        </p>
                    </div>
                    <span class="nb-kicker border-2 border-black px-2 py-1 {{ $sc['tone'] }}">{{ ucfirst($pemesanan->status) }}</span>
                </div>
            </section>

            <!-- Informasi Rekening & Bukti Pembayaran -->
            <section class="nb-card p-5 md:p-6">
                <h3 class="text-3xl font-black leading-none">Pembayaran & Bukti Transfer</h3>
                <p class="nb-kicker mt-1">Lakukan transfer ke rekening pemilik kos di bawah ini, lalu unggah bukti transfer.</p>

                <div class="nb-card-soft mt-4 p-4">
                    <p class="nb-kicker mb-2">Rekening Tujuan Transfer</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <span class="nb-kicker">Bank / E-Wallet</span>
                            <p class="text-xl font-black">{{ $pemesanan->kosan->rekening_bank ?? 'Hubungi Pemilik' }}</p>
                        </div>
                        <div>
                            <span class="nb-kicker">Nomor Rekening</span>
                            <p class="text-xl font-black">{{ $pemesanan->kosan->rekening_nomor ?? ($pemesanan->kosan->pemilik->no_telepon ?? '-') }}</p>
                        </div>
                        <div>
                            <span class="nb-kicker">Atas Nama</span>
                            <p class="text-xl font-black">{{ $pemesanan->kosan->rekening_nama ?? ($pemesanan->kosan->pemilik->nama ?? '-') }}</p>
                        </div>
                    </div>
                    <div class="mt-3 border-t-2 border-black pt-3 flex justify-between items-center">
                        <span class="font-bold">Total Pembayaran:</span>
                        <span class="text-2xl font-black">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <p class="nb-kicker">Status Bukti Transfer</p>
                        @if($pemesanan->bukti_pembayaran_url)
                            <a href="{{ $pemesanan->bukti_pembayaran_url }}" target="_blank" class="nb-btn nb-btn-secondary py-1 px-3 text-sm whitespace-nowrap">
                                Buka Foto Penuh ↗
                            </a>
                        @endif
                    </div>
                    @if($pemesanan->bukti_pembayaran_url)
                        <div class="nb-card-soft p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-20 w-20 shrink-0 border-2 border-black overflow-hidden bg-gray-200">
                                    <img src="{{ $pemesanan->bukti_pembayaran_url }}" alt="Bukti Transfer" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-lg">Bukti Transfer Terunggah</p>
                                    <p class="nb-kicker">Menunggu verifikasi pemilik</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="nb-card-soft border-2 border-dashed border-red-500 bg-red-50 p-4 text-center">
                            <p class="text-lg font-bold text-red-700">Belum Ada Bukti Transfer</p>
                            <p class="text-sm font-medium text-gray-700 mt-1">Silakan transfer dan unggah foto bukti struk/screenshot transfer Anda agar pesanan dapat segera diproses.</p>
                        </div>
                    @endif

                    @if($pemesanan->status === 'pending')
                        <form method="POST"
                              action="{{ route('user.pemesanan.upload_bukti', $pemesanan) }}"
                              enctype="multipart/form-data"
                              class="mt-4 space-y-3"
                              x-data="{
                                  fileName: '',
                                  fileSize: '',
                                  previewUrl: '',
                                  handleFile(e) {
                                      const file = e.target.files[0];
                                      if (file) {
                                          this.fileName = file.name;
                                          this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                                          const reader = new FileReader();
                                          reader.onload = (ev) => { this.previewUrl = ev.target.result; };
                                          reader.readAsDataURL(file);
                                      } else {
                                          this.resetFile();
                                      }
                                  },
                                  resetFile() {
                                      this.fileName = '';
                                      this.fileSize = '';
                                      this.previewUrl = '';
                                      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                                  }
                              }">
                            @csrf
                            <label class="nb-label">{{ $pemesanan->bukti_pembayaran ? 'Ganti / Unggah Ulang Bukti Transfer' : 'Unggah Bukti Transfer Sekarang' }}</label>

                            <input type="file"
                                   name="bukti_pembayaran"
                                   accept="image/*"
                                   x-ref="fileInput"
                                   class="hidden"
                                   @change="handleFile($event)">

                            <!-- Tampilan saat belum pilih file -->
                            <div x-show="!fileName" class="flex flex-wrap items-center gap-3">
                                <button type="button"
                                        @click="$refs.fileInput.click()"
                                        class="nb-btn bg-white hover:bg-gray-50 flex items-center gap-2">
                                    <span>📁</span> Pilih File Bukti Transfer
                                </button>
                                <span class="text-sm font-medium text-gray-500">Format: JPG, PNG (Maks 10MB)</span>
                            </div>

                            <!-- Tampilan saat SUDAH pilih file (tidak menampilkan 'Choose file') -->
                            <div x-show="fileName" x-cloak class="nb-card-soft p-3 border-2 border-green-600 bg-green-50 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <template x-if="previewUrl">
                                            <div class="h-14 w-14 shrink-0 border-2 border-black overflow-hidden bg-white">
                                                <img :src="previewUrl" alt="Preview Bukti" class="h-full w-full object-cover">
                                            </div>
                                        </template>
                                        <div class="min-w-0">
                                            <p class="font-bold text-base truncate text-gray-900" x-text="fileName"></p>
                                            <p class="nb-kicker text-xs text-gray-600" x-text="fileSize"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="$refs.fileInput.click()"
                                                class="nb-btn py-1 px-3 text-sm bg-white">
                                            Ganti File
                                        </button>
                                        <button type="button"
                                                @click="resetFile()"
                                                class="nb-btn py-1 px-3 text-sm bg-red-100 text-red-700 border-red-700 hover:bg-red-200">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-green-200 flex justify-end">
                                    <button type="submit" class="nb-btn nb-btn-primary whitespace-nowrap">
                                        Kirim Bukti Pembayaran
                                    </button>
                                </div>
                            </div>

                            @error('bukti_pembayaran')<p class="nb-error">{{ $message }}</p>@enderror
                        </form>
                    @endif
                </div>

                <div class="mt-4 border-2 border-black bg-yellow-100 p-3 text-sm font-bold">
                    ⚡ <strong>Aturan Alokasi Kamar (First-Approved):</strong> Pemilik kos akan memverifikasi dan menyetujui pemesanan berdasarkan yang bukti transfernya masuk terlebih dahulu. Jika kamar habis, pesanan pending lainnya otomatis dibatalkan.
                </div>
            </section>

            <section class="nb-card p-5 md:p-6">
                <h3 class="text-3xl font-black leading-none">Kosan yang Dipesan</h3>
                <div class="mt-5 grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-stretch">
                    <div class="overflow-hidden border-2 border-black bg-gray-200">
                        @if($pemesanan->kosan->fotoUtama)
                            <img src="{{ $pemesanan->kosan->fotoUtama->url }}" alt="{{ $pemesanan->kosan->nama_kosan }}" class="h-full min-h-56 w-full object-cover">
                        @else
                            <div class="flex h-full min-h-56 items-center justify-center px-4 text-center">
                                <p class="text-2xl font-black">Foto belum tersedia</p>
                            </div>
                        @endif
                    </div>
                    <div class="nb-card-soft p-4 md:p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('kosan.show', $pemesanan->kosan) }}" class="block text-3xl font-black leading-none">
                                    {{ $pemesanan->kosan->nama_kosan }}
                                </a>
                                <p class="mt-2 text-lg font-medium">{{ $pemesanan->kosan->alamat }}, {{ $pemesanan->kosan->kota }}</p>
                            </div>
                            <span class="nb-kicker border-2 border-black px-2 py-1">
                                {{ ucfirst($pemesanan->kosan->tipe) }}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div class="nb-card bg-white p-3">
                                <p class="nb-kicker">Harga</p>
                                <p class="mt-1 text-2xl font-black">Rp {{ number_format($pemesanan->kosan->harga_per_bulan, 0, ',', '.') }}</p>
                                <p class="nb-kicker mt-1">per bulan</p>
                            </div>
                            <div class="nb-card bg-white p-3">
                                <p class="nb-kicker">Pemilik</p>
                                <p class="mt-1 text-2xl font-black">{{ $pemesanan->kosan->pemilik->nama ?? '-' }}</p>
                                @if($pemesanan->kosan->pemilik->no_telepon)
                                    <p class="nb-kicker mt-1">{{ $pemesanan->kosan->pemilik->no_telepon }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="nb-card p-5 md:p-6">
                <h3 class="text-3xl font-black leading-none">Rincian</h3>
                <div class="mt-5 space-y-3 text-lg font-medium">
                    <div class="flex items-center justify-between border-b-2 border-black pb-2">
                        <span>No. Pemesanan</span>
                        <span class="font-black">#{{ str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b-2 border-black pb-2">
                        <span>Tanggal Pesan</span>
                        <span class="font-black">{{ $pemesanan->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b-2 border-black pb-2">
                        <span>Tanggal Masuk</span>
                        <span class="font-black">{{ \Carbon\Carbon::parse($pemesanan->tanggal_masuk)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b-2 border-black pb-2">
                        <span>Durasi</span>
                        <span class="font-black">{{ $pemesanan->durasi_bulan }} bulan</span>
                    </div>
                    @if($pemesanan->catatan)
                        <div>
                            <span class="nb-kicker">Catatan / Keterangan</span>
                            <p class="nb-card-soft mt-2 p-3 text-lg font-medium">{{ $pemesanan->catatan }}</p>
                        </div>
                    @endif
                </div>
            </section>

            <div class="flex gap-3">
                <a href="{{ route('user.dashboard') }}" class="nb-btn flex-1">Kembali</a>
                @if($pemesanan->status === 'pending')
                    <form method="POST" action="{{ route('user.pemesanan.destroy', $pemesanan) }}" class="flex-1" onsubmit="return confirm('Yakin ingin membatalkan pemesanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="nb-btn nb-btn-danger w-full">Batalkan Pemesanan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
