<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.kosan.index') }}" class="nb-btn py-1 px-2 text-base">Kembali</a>
            <span class="nb-kicker">/</span>
            <span class="text-3xl font-black leading-none">Tambah Kosan</span>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell max-w-3xl">
            <form method="POST" action="{{ route('admin.kosan.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Pemilik</h3>
                    <div class="mt-5">
                        <label class="nb-label">Pilih Pemilik</label>
                        <select name="user_id" class="nb-select bg-white">
                            <option value="">Pilih pemilik</option>
                            @foreach($pemilikList as $pemilik)
                                <option value="{{ $pemilik->id }}" @selected(old('user_id') == $pemilik->id)>{{ $pemilik->nama }} ({{ $pemilik->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="nb-error">{{ $message }}</p>@enderror
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Informasi Kosan</h3>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="nb-label">Nama Kosan</label>
                            <input type="text" name="nama_kosan" value="{{ old('nama_kosan') }}" placeholder="Contoh: Kos Bu Ani" class="nb-input">
                            @error('nama_kosan')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Deskripsi</label>
                            <textarea name="deskripsi" rows="5" placeholder="Jelaskan tentang kosan..." class="nb-textarea">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="nb-label">Alamat</label>
                                <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Jl. Contoh No. 1" class="nb-input">
                                @error('alamat')<p class="nb-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="nb-label">Kota</label>
                                <input type="text" name="kota" value="{{ old('kota') }}" placeholder="Yogyakarta" class="nb-input">
                                @error('kota')<p class="nb-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Detail Harga</h3>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="nb-label">Harga per Bulan</label>
                            <input type="number" name="harga_per_bulan" value="{{ old('harga_per_bulan') }}" placeholder="500000" class="nb-input">
                            @error('harga_per_bulan')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Tipe Kosan</label>
                            <select name="tipe" class="nb-select bg-white">
                                <option value="">Pilih tipe</option>
                                <option value="putra" @selected(old('tipe') === 'putra')>Putra</option>
                                <option value="putri" @selected(old('tipe') === 'putri')>Putri</option>
                                <option value="campur" @selected(old('tipe') === 'campur')>Campur</option>
                            </select>
                            @error('tipe')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Jumlah Kamar</label>
                            <input type="number" name="jumlah_kamar" value="{{ old('jumlah_kamar') }}" min="1" placeholder="10" class="nb-input">
                            @error('jumlah_kamar')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="nb-label">Kamar Tersedia</label>
                            <input type="number" name="kamar_tersedia" value="{{ old('kamar_tersedia') }}" min="0" placeholder="5" class="nb-input">
                            @error('kamar_tersedia')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="nb-label">Status</label>
                            <select name="status" class="nb-select bg-white">
                                <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                                <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
                            </select>
                            @error('status')<p class="nb-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Fasilitas</h3>
                    @php
                        $oldFasilitas = old('fasilitas', []);
                    @endphp
                    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3">
                        @foreach($fasilitasList as $fasilitas)
                            <label class="nb-card-soft flex items-center gap-2 px-3 py-2">
                                <input type="checkbox" name="fasilitas[]" value="{{ $fasilitas }}" @checked(in_array($fasilitas, $oldFasilitas)) class="h-5 w-5 border-2 border-black">
                                <span class="text-lg font-semibold">{{ $fasilitas }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="nb-card p-5 md:p-6">
                    <h3 class="text-3xl font-black leading-none">Foto Kosan</h3>
                    <p class="nb-kicker mt-1">Upload minimal 1 foto. JPG, PNG, GIF. Maks 10MB/foto.</p>
                    <label for="fotos" class="nb-card-soft mt-5 flex cursor-pointer flex-col items-center justify-center border-2 border-dashed border-black p-6 text-center">
                        <p class="text-2xl font-black">Pilih Foto</p>
                        <p class="mt-2 text-lg font-medium">Klik untuk unggah foto baru</p>
                        <input type="file" id="fotos" name="fotos[]" multiple accept="image/*" class="hidden" onchange="previewFotos(this)">
                    </label>
                    @error('fotos.*')<p class="nb-error mt-2">{{ $message }}</p>@enderror
                    <p id="file-count" class="nb-kicker mt-2 hidden"></p>
                    <div id="foto-preview" class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4 hidden"></div>
                </section>

                <div class="flex flex-col gap-3 md:flex-row">
                    <a href="{{ route('admin.kosan.index') }}" class="nb-btn w-full md:w-auto md:flex-1">Batal</a>
                    <button type="submit" class="nb-btn nb-btn-primary w-full md:w-auto md:flex-1">Simpan Kosan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewFotos(input) {
            const preview = document.getElementById('foto-preview');
            const counter = document.getElementById('file-count');
            preview.innerHTML = '';

            if (input.files && input.files.length > 0) {
                preview.classList.remove('hidden');
                counter.classList.remove('hidden');
                counter.textContent = input.files.length + ' foto dipilih';

                Array.from(input.files).forEach(function (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.innerHTML = `<img src="${e.target.result}" class="h-28 w-full border-2 border-black object-cover">`;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                preview.classList.add('hidden');
                counter.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
