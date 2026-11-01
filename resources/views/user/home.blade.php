<x-app-layout>
    <x-slot name="header">
        <p class="nb-kicker">Daftar</p>
        <h2 class="text-4xl font-black leading-none">Beranda Kosan</h2>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell">
            @php
                $selectedFasilitas = (array) request('fasilitas', []);
                $hasActiveFilters = request()->filled('q') || request()->filled('tipe') || request()->filled('harga_max') || !empty($selectedFasilitas);
                $activeFilterCount = (request('tipe') ? 1 : 0) + (request('harga_max') ? 1 : 0) + count($selectedFasilitas);
            @endphp

            <form method="GET" action="{{ route('kosan.search') }}" class="nb-card p-4 md:p-6" x-data="{ 
                showFilters: {{ (request()->filled('tipe') || request()->filled('harga_max') || !empty($selectedFasilitas)) ? 'true' : 'false' }} 
            }">
                <div class="flex flex-col gap-3 md:flex-row">
                    <div class="relative flex-1">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg select-none" aria-hidden="true">🔍</span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kosan, kota, atau alamat..."
                               id="q" class="nb-input pl-11" />
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showFilters = !showFilters" 
                                :class="showFilters ? 'bg-yellow-300' : ''"
                                class="nb-btn nb-btn-secondary whitespace-nowrap flex items-center gap-2">
                            <span>⚙️</span>
                            <span x-text="showFilters ? 'Tutup Filter' : 'Filter'"></span>
                            @if($activeFilterCount > 0)
                                <span class="bg-black text-white text-xs font-black px-2 py-0.5 border border-black leading-none">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </button>
                        <button type="submit" class="nb-btn nb-btn-primary whitespace-nowrap">Cari Kosan</button>
                    </div>
                </div>

                <div x-show="showFilters" x-transition class="mt-5 border-t-2 border-black pt-5 space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Tipe Kos -->
                        <div>
                            <label class="nb-kicker font-black mb-1.5 block">Tipe Kos</label>
                            <select name="tipe" class="nb-select">
                                <option value="">Semua Tipe (Putra, Putri, Campur)</option>
                                <option value="putra" {{ request('tipe') == 'putra' ? 'selected' : '' }}>👦 Kosan Putra</option>
                                <option value="putri" {{ request('tipe') == 'putri' ? 'selected' : '' }}>👧 Kosan Putri</option>
                                <option value="campur" {{ request('tipe') == 'campur' ? 'selected' : '' }}>👫 Kosan Campur</option>
                            </select>
                        </div>

                        <!-- Harga Max -->
                        <div>
                            <label class="nb-kicker font-black mb-1.5 block">Harga Maksimal (/bulan)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-gray-500 select-none">Rp</span>
                                <input type="number" name="harga_max" value="{{ request('harga_max') }}" placeholder="Contoh: 1500000" class="nb-input pl-11">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Fasilitas -->
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b-2 border-black pb-2 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xl" aria-hidden="true">🛋️</span>
                                <label class="nb-kicker font-black text-sm">Filter Berdasarkan Fasilitas</label>
                            </div>
                            <span class="text-xs font-bold text-gray-600 font-mono">Pilih satu atau beberapa fasilitas</span>
                        </div>

                        @php
                            $facilityIcons = [
                                'wifi' => '📶',
                                'ac' => '❄️',
                                'kamar mandi dalam' => '🚿',
                                'kamar mandi luar' => '🚪',
                                'dapur' => '🍳',
                                'dapur bersama' => '🍳',
                                'parkir motor' => '🛵',
                                'parkir mobil' => '🚗',
                                'kasur' => '🛏️',
                                'lemari' => '🗄️',
                                'meja belajar' => '🪑',
                                'tv' => '📺',
                                'water heater' => '♨️',
                                'laundry' => '🧺',
                                'cctv' => '📹',
                                'kulkas' => '🧊',
                                'keamanan' => '🛡️',
                                'balkon' => '🌇',
                                'cleaning service' => '🧹',
                            ];
                        @endphp

                        <div class="flex flex-wrap gap-2.5">
                            @forelse($fasilitasList as $f)
                                @php
                                    $icon = $facilityIcons[strtolower(trim($f))] ?? '✨';
                                    $isChecked = in_array($f, $selectedFasilitas);
                                @endphp
                                <label class="group flex items-center gap-2 px-3 py-2 border-2 border-black font-bold text-base transition-all cursor-pointer bg-white text-gray-900 shadow-[3px_3px_0_#111827] hover:bg-yellow-50 hover:translate-x-[-1px] hover:translate-y-[-1px] has-[:checked]:bg-yellow-300 has-[:checked]:shadow-[3px_3px_0_#111827]">
                                    <input type="checkbox" 
                                           name="fasilitas[]" 
                                           value="{{ $f }}" 
                                           @checked($isChecked)
                                           class="w-5 h-5 border-2 border-black rounded-none text-black accent-black cursor-pointer">
                                    <span class="text-base leading-none" aria-hidden="true">{{ $icon }}</span>
                                    <span class="select-none leading-tight">{{ $f }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 font-medium">Tidak ada data fasilitas.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tombol Aksi Filter -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t-2 border-black">
                        <div>
                            @if($hasActiveFilters)
                                <a href="{{ route('home') }}" class="nb-btn py-1.5 px-3 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700">
                                    ✕ Reset Semua Filter
                                </a>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showFilters = false" class="nb-btn py-2 px-4 text-sm bg-white">
                                Selesai
                            </button>
                            <button type="submit" class="nb-btn nb-btn-secondary py-2 px-6 text-sm">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @if($hasActiveFilters)
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="nb-kicker text-xs font-bold text-gray-600">Filter Aktif:</span>

                    @if(request('q'))
                        <span class="inline-flex items-center gap-1.5 border-2 border-black bg-white px-2.5 py-1 text-sm font-bold shadow-[2px_2px_0_#111827]">
                            <span>Pencarian: "{{ request('q') }}"</span>
                        </span>
                    @endif

                    @if(request('tipe'))
                        <span class="inline-flex items-center gap-1.5 border-2 border-black bg-blue-100 px-2.5 py-1 text-sm font-bold shadow-[2px_2px_0_#111827]">
                            <span>Tipe: {{ ucfirst(request('tipe')) }}</span>
                        </span>
                    @endif

                    @if(request('harga_max'))
                        <span class="inline-flex items-center gap-1.5 border-2 border-black bg-green-100 px-2.5 py-1 text-sm font-bold shadow-[2px_2px_0_#111827]">
                            <span>Maks: Rp {{ number_format(request('harga_max'), 0, ',', '.') }}</span>
                        </span>
                    @endif

                    @foreach($selectedFasilitas as $sf)
                        <span class="inline-flex items-center gap-1.5 border-2 border-black bg-yellow-200 px-2.5 py-1 text-sm font-bold shadow-[2px_2px_0_#111827]">
                            <span>✓ {{ $sf }}</span>
                        </span>
                    @endforeach

                    <a href="{{ route('home') }}" class="nb-kicker text-xs font-black underline hover:text-red-600 ml-1">
                        Hapus Semua
                    </a>
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($kosans as $kosan)
                    <a href="{{ route('kosan.show', $kosan->id) }}"
                        class="nb-card nb-card-hover overflow-hidden flex flex-col justify-between">
                        <div>
                            @if($kosan->fotoUtama)
                                <img src="{{ $kosan->fotoUtama->url }}" alt="{{ $kosan->nama_kosan }}"
                                     class="h-48 w-full object-cover">
                            @else
                                <div class="flex h-48 items-center justify-center bg-[#E5E7EB] text-xl font-bold">
                                    Tidak Ada Foto
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <span
                                        class="nb-kicker border-2 border-black px-2 py-1 {{ $kosan->tipe === 'putra' ? 'bg-blue-200' : ($kosan->tipe === 'putri' ? 'bg-pink-200' : 'bg-yellow-200') }}">
                                        {{ ucfirst($kosan->tipe) }}
                                    </span>
                                    <span class="nb-kicker font-bold">{{ $kosan->kamar_tersedia }} kamar</span>
                                </div>
                                <h3 class="text-3xl font-black leading-none">{{ $kosan->nama_kosan }}</h3>
                                <p class="mt-2 text-lg font-medium text-gray-700">
                                    📍 {{ $kosan->kota }}
                                </p>

                                @if(!empty($kosan->fasilitas) && is_array($kosan->fasilitas))
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach(array_slice($kosan->fasilitas, 0, 3) as $f)
                                            <span class="nb-kicker border border-black px-1.5 py-0.5 text-xs font-bold {{ in_array($f, $selectedFasilitas) ? 'bg-yellow-300 font-black' : 'bg-gray-100' }}">
                                                {{ $f }}
                                            </span>
                                        @endforeach
                                        @if(count($kosan->fasilitas) > 3)
                                            <span class="nb-kicker text-xs text-gray-500 font-bold self-center">+{{ count($kosan->fasilitas) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="px-4 pb-4 pt-2 border-t-2 border-gray-100 flex items-baseline justify-between">
                            <span class="nb-kicker text-xs text-gray-500">Mulai dari</span>
                            <p class="text-2xl font-black">
                                Rp {{ number_format($kosan->harga_per_bulan, 0, ',', '.') }}
                                <span class="nb-kicker text-sm">/bln</span>
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="nb-card col-span-full p-10 text-center">
                        <p class="nb-kicker">Kosong</p>
                        <p class="mt-2 text-4xl font-black">Belum Ada Kosan Tersedia</p>
                        <p class="mt-2 text-xl font-medium">Coba ubah filter atau kata kunci pencarian Anda.</p>
                        <a href="{{ route('home') }}" class="nb-btn nb-btn-secondary mt-4 inline-block">
                            Lihat Semua Kosan
                        </a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($kosans, 'hasPages') && $kosans->hasPages())
                <div class="mt-8">{{ $kosans->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
