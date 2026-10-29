<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <span class="nb-kicker">Admin</span>
            <span class="nb-kicker">/</span>
            <h2 class="text-4xl font-black leading-none">Ulasan</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b-2 border-black bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Kosan</th>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Rating</th>
                                <th class="px-4 py-3">Komentar</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ulasans as $ulasan)
                                <tr class="border-b border-black/10">
                                    <td class="px-4 py-3 font-semibold">{{ $ulasan->kosan->nama_kosan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $ulasan->user->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="nb-kicker border-2 border-black px-2 py-1">{{ $ulasan->rating }}/5</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $ulasan->komentar ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('admin.ulasan.destroy', $ulasan) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="nb-btn nb-btn-danger py-1 px-3 text-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-lg font-semibold">
                                        Belum ada ulasan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ulasans->hasPages())
                    <div class="border-t-2 border-black p-4">
                        {{ $ulasans->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
