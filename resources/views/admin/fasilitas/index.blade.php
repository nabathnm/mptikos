<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <span class="nb-kicker">Admin</span>
            <span class="nb-kicker">/</span>
            <h2 class="text-4xl font-black leading-none">Fasilitas</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card p-5 md:p-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="nb-kicker">Master fasilitas untuk pilihan form kosan</p>
                </div>
                <a href="{{ route('admin.fasilitas.create') }}" class="nb-btn nb-btn-primary">Tambah Fasilitas</a>
            </section>

            <section class="nb-card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b-2 border-black bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Nama Fasilitas</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fasilitas as $item)
                                <tr class="border-b border-black/10">
                                    <td class="px-4 py-3 font-semibold">{{ $item->nama_fasilitas }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.fasilitas.edit', $item) }}" class="nb-btn py-1 px-3 text-sm">Edit</a>
                                            <form action="{{ route('admin.fasilitas.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="nb-btn nb-btn-danger py-1 px-3 text-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-lg font-semibold">
                                        Belum ada fasilitas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($fasilitas->hasPages())
                    <div class="border-t-2 border-black p-4">
                        {{ $fasilitas->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
