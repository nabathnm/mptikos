<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Pemesanan;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinary
    ) {}

    /**
     * Dashboard: daftar semua pemesanan user
     */
    public function dashboard()
    {
        $pemesanans = Pemesanan::with(['kosan.fotoUtama', 'kosan.pemilik'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.dashboard', compact('pemesanans'));
    }

    public function index()
    {
        return redirect()->route('user.dashboard');
    }

    public function create(Request $request)
    {
        $kosan = Kosan::with(['fotoUtama', 'pemilik'])
            ->findOrFail($request->kosan_id);

        if ($kosan->kamar_tersedia <= 0) {
            return back()->with('error', 'Maaf, kamar sudah penuh.');
        }

        return view('user.pemesanan.create', compact('kosan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kosan_id'         => 'required|exists:kosans,id',
            'tanggal_masuk'    => 'required|date|after_or_equal:today',
            'durasi_bulan'     => 'required|integer|min:1|max:24',
            'catatan'          => 'nullable|string|max:500',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $kosan = Kosan::findOrFail($request->kosan_id);

        if ($kosan->kamar_tersedia <= 0) {
            return back()->with('error', 'Maaf, kamar sudah penuh.')->withInput();
        }

        // Cek jika user sudah punya pemesanan aktif di kos yang sama
        $sudahAktif = Pemesanan::where('user_id', Auth::id())
            ->where('kosan_id', $kosan->id)
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($sudahAktif) {
            return back()->with('error', 'Anda sudah memiliki pemesanan aktif di kosan ini.')->withInput();
        }

        $totalHarga = $kosan->harga_per_bulan * $request->durasi_bulan;

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $this->cloudinary->upload($request->file('bukti_pembayaran'), 'bukti_pembayaran');
        }

        $pemesanan = Pemesanan::create([
            'user_id'          => Auth::id(),
            'kosan_id'         => $kosan->id,
            'tanggal_masuk'    => $request->tanggal_masuk,
            'durasi_bulan'     => $request->durasi_bulan,
            'total_harga'      => $totalHarga,
            'status'           => 'pending',
            'catatan'          => $request->catatan,
            'bukti_pembayaran' => $buktiPath,
        ]);

        return redirect()->route('user.pemesanan.show', $pemesanan->id)
            ->with('success', 'Pemesanan berhasil diajukan! Menunggu persetujuan pemilik kos.');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $pemesanan = Pemesanan::where('user_id', Auth::id())->findOrFail($id);

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Bukti pembayaran hanya dapat diunggah untuk pesanan yang berstatus pending.');
        }

        // Hapus bukti lama jika ada
        if (!empty($pemesanan->bukti_pembayaran)) {
            $this->cloudinary->delete($pemesanan->bukti_pembayaran);
        }

        $path = $this->cloudinary->upload($request->file('bukti_pembayaran'), 'bukti_pembayaran');
        $pemesanan->update(['bukti_pembayaran' => $path]);

        return back()->with('success', 'Bukti transfer pembayaran berhasil diunggah! Menunggu verifikasi pemilik kos.');
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::with(['kosan.fotoUtama', 'kosan.pemilik'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.pemesanan.show', compact('pemesanan'));
    }

    public function destroy($id)
    {
        $pemesanan = Pemesanan::where('user_id', Auth::id())->findOrFail($id);

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Hanya pemesanan berstatus pending yang dapat dibatalkan.');
        }

        if (!empty($pemesanan->bukti_pembayaran)) {
            $this->cloudinary->delete($pemesanan->bukti_pembayaran);
        }

        $pemesanan->delete();

        return redirect()->route('user.dashboard')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
