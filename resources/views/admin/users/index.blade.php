<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <span class="nb-kicker">Admin</span>
            <span class="nb-kicker">/</span>
            <h2 class="text-4xl font-black leading-none">Users</h2>
        </div>
    </x-slot>

    <div class="pt-6">
        <div class="nb-shell space-y-6">
            <section class="nb-card p-5 md:p-6">
                <form method="GET" class="grid gap-3 md:grid-cols-[1fr_200px_auto] md:items-end">
                    <div>
                        <label class="nb-label">Cari nama/email</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="nb-input" placeholder="Cari user...">
                    </div>
                    <div>
                        <label class="nb-label">Role</label>
                        <select name="role" class="nb-select bg-white">
                            <option value="">Semua</option>
                            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                            <option value="pemilik" @selected(request('role') === 'pemilik')>Pemilik</option>
                            <option value="user" @selected(request('role') === 'user')>User</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="nb-btn nb-btn-primary">Filter</button>
                        <a href="{{ route('admin.users.create') }}" class="nb-btn nb-btn-secondary">Tambah User</a>
                    </div>
                </form>
            </section>

            <section class="nb-card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b-2 border-black bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Telepon</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-b border-black/10">
                                    <td class="px-4 py-3 font-semibold">{{ $user->nama }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="nb-kicker border-2 border-black px-2 py-1">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $user->no_telepon ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="nb-btn py-1 px-3 text-sm">Edit</a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="nb-btn nb-btn-danger py-1 px-3 text-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-lg font-semibold">
                                        Belum ada user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="border-t-2 border-black p-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
