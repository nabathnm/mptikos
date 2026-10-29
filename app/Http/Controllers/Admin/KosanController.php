<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\FotoKosan;
use App\Models\Kosan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KosanController extends Controller
{
    public function index()
    {
        $kosans = Kosan::with(['fotoUtama', 'pemilik'])
            ->latest()
            ->paginate(12);

        return view('admin.kosan.index', compact('kosans'));
    }

    public function create()
    {
        $pemilikList = User::where('role', 'pemilik')->orderBy('nama')->get();
        $fasilitasList = Fasilitas::listForForm();

        return view('admin.kosan.create', compact('pemilikList', 'fasilitasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'pemilik'),
            ],
            'nama_kosan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string',
            'harga_per_bulan' => 'required|numeric',
            'jumlah_kamar' => 'required|integer',
            'kamar_tersedia' => 'required|integer',
            'tipe' => 'required|in:putra,putri,campur',
            'status' => 'required|in:aktif,nonaktif',
            'fasilitas' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $kosan = Kosan::create([
            'user_id' => $request->user_id,
            'nama_kosan' => $request->nama_kosan,
            'deskripsi' => $request->deskripsi,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'harga_per_bulan' => $request->harga_per_bulan,
            'jumlah_kamar' => $request->jumlah_kamar,
            'kamar_tersedia' => $request->kamar_tersedia,
            'tipe' => $request->tipe,
            'fasilitas' => $request->fasilitas ?? [],
            'status' => $request->status,
        ]);

        if ($request->hasFile('fotos')) {
            $is_utama = true;
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('foto_kosan', 'public');
                FotoKosan::create([
                    'kosan_id' => $kosan->id,
                    'foto' => $path,
                    'is_utama' => $is_utama,
                ]);
                $is_utama = false;
            }
        }

        return redirect()->route('admin.kosan.index')->with('success', 'Kosan berhasil ditambahkan.');
    }

    public function show(Kosan $kosan)
    {
        $kosan->load(['fotos', 'pemilik']);

        return view('admin.kosan.show', compact('kosan'));
    }

    public function edit(Kosan $kosan)
    {
        $kosan->load(['fotos', 'pemilik']);

        $pemilikList = User::where('role', 'pemilik')->orderBy('nama')->get();
        $fasilitasList = array_values(array_unique(array_merge(
            Fasilitas::listForForm(),
            $kosan->fasilitas ?? []
        )));

        return view('admin.kosan.edit', compact('kosan', 'pemilikList', 'fasilitasList'));
    }

    public function update(Request $request, Kosan $kosan)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'pemilik'),
            ],
            'nama_kosan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string',
            'harga_per_bulan' => 'required|numeric',
            'jumlah_kamar' => 'required|integer',
            'kamar_tersedia' => 'required|integer',
            'tipe' => 'required|in:putra,putri,campur',
            'status' => 'required|in:aktif,nonaktif',
            'fasilitas' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'hapus_fotos' => 'nullable|array',
            'hapus_fotos.*' => 'exists:foto_kosans,id',
            'foto_utama_id' => 'nullable|exists:foto_kosans,id',
        ]);

        $kosan->update([
            'user_id' => $request->user_id,
            'nama_kosan' => $request->nama_kosan,
            'deskripsi' => $request->deskripsi,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'harga_per_bulan' => $request->harga_per_bulan,
            'jumlah_kamar' => $request->jumlah_kamar,
            'kamar_tersedia' => $request->kamar_tersedia,
            'tipe' => $request->tipe,
            'fasilitas' => $request->fasilitas ?? [],
            'status' => $request->status,
        ]);

        if ($request->has('hapus_fotos')) {
            $fotosToDelete = FotoKosan::whereIn('id', $request->hapus_fotos)
                ->where('kosan_id', $kosan->id)
                ->get();
            foreach ($fotosToDelete as $foto) {
                Storage::disk('public')->delete($foto->foto);
                $foto->delete();
            }
        }

        if ($request->has('foto_utama_id') && ! in_array($request->foto_utama_id, $request->hapus_fotos ?? [], true)) {
            $kosan->fotos()->update(['is_utama' => false]);
            $kosan->fotos()->where('id', $request->foto_utama_id)->update(['is_utama' => true]);
        }

        if ($request->hasFile('fotos')) {
            $is_utama = $kosan->fotos()->where('is_utama', true)->count() === 0;
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('foto_kosan', 'public');
                FotoKosan::create([
                    'kosan_id' => $kosan->id,
                    'foto' => $path,
                    'is_utama' => $is_utama,
                ]);
                $is_utama = false;
            }
        }

        if ($kosan->fotos()->count() > 0 && $kosan->fotos()->where('is_utama', true)->count() === 0) {
            $firstFoto = $kosan->fotos()->first();
            $firstFoto->update(['is_utama' => true]);
        }

        return redirect()->route('admin.kosan.index')->with('success', 'Kosan berhasil diperbarui.');
    }

    public function destroy(Kosan $kosan)
    {
        foreach ($kosan->fotos as $foto) {
            Storage::disk('public')->delete($foto->foto);
        }

        $kosan->delete();

        return redirect()->route('admin.kosan.index')->with('success', 'Kosan berhasil dihapus.');
    }
}
