<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Kosan;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosanController extends Controller
{
    public function show($id)
    {
        $kosan = Kosan::with(['pemilik:id,nama,no_telepon', 'fotoUtama', 'fotos', 'ulasans.user:id,nama'])
            ->findOrFail($id);

        $ulasans = $kosan->ulasans;
        $ratingRata = $ulasans->avg('rating') ?? 0;
        $totalUlasan = $ulasans->count();

        // Cek apakah user sudah pernah memberi ulasan & bisa mengulas
        $sudahUlasan = false;
        $bisaUlas = false;
        if (Auth::check()) {
            $sudahUlasan = $ulasans->where('user_id', Auth::id())->isNotEmpty();
            // Cek apakah ada pesanan disetujui
            $bisaUlas = Pemesanan::where('user_id', Auth::id())
                ->where('kosan_id', $id)
                ->where('status', 'disetujui')
                ->exists();
        }

        return view('user.show', compact('kosan', 'ratingRata', 'totalUlasan', 'sudahUlasan', 'bisaUlas'));
    }

    public function index(Request $request)
    {
        $query = Kosan::with('fotoUtama')
            ->where('status', 'aktif')
            ->where('kamar_tersedia', '>', 0);

        $kosans = $query->latest()->paginate(12);

        $fasilitasList = Fasilitas::orderBy('nama_fasilitas')->get();

        return view('user.home', compact('kosans', 'fasilitasList'));
    }

    public function search(Request $request)
    {
        $query = Kosan::with('fotoUtama')
            ->where('status', 'aktif')
            ->where('kamar_tersedia', '>', 0);

        // Filter pencarian
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_kosan', 'like', '%' . $request->q . '%')
                  ->orWhere('kota', 'like', '%' . $request->q . '%')
                  ->orWhere('alamat', 'like', '%' . $request->q . '%');
            });
        }
        if ($request->filled('kota')) {
            $query->where('kota', 'like', '%' . $request->kota . '%');
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('min_harga')) {
            $query->where('harga_per_bulan', '>=', $request->min_harga);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga_per_bulan', '<=', $request->harga_max);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga_per_bulan', '<=', $request->max_harga);
        }
        if ($request->filled('fasilitas')) {
            $fasilitas = is_array($request->fasilitas) ? $request->fasilitas : [$request->fasilitas];
            foreach ($fasilitas as $f) {
                $query->whereJsonContains('fasilitas', $f);
            }
        }

        $kosans = $query->latest()->paginate(12);

        $fasilitasList = Fasilitas::orderBy('nama_fasilitas')->get();

        return view('user.home', compact('kosans', 'fasilitasList'));
    }

    public function ulasan(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string'
        ]);

        // Cek apakah user pernah menyewa kos ini dan disetujui
        $sudahSewa = Pemesanan::where('user_id', Auth::id())
            ->where('kosan_id', $id)
            ->where('status', 'disetujui')
            ->exists();

        if (!$sudahSewa) {
            return back()->with('error', 'Anda hanya dapat mengulas kosan yang pernah Anda sewa dan disetujui.');
        }

        // Cek sudah pernah mengulas
        $sudahUlas = Ulasan::where('user_id', Auth::id())
            ->where('kosan_id', $id)
            ->exists();

        if ($sudahUlas) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk kosan ini.');
        }

        Ulasan::create([
            'user_id'  => Auth::id(),
            'kosan_id' => $id,
            'rating'   => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Ulasan berhasil ditambahkan!');
    }
}
