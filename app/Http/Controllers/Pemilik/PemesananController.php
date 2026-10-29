<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index()
    {
        $pemesanans = Pemesanan::whereHas('kosan', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['user', 'kosan.fotoUtama'])
            ->latest()
            ->paginate(10);

        return view('pemilik.pemesanan.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan)
    {
        // Pastikan pemesanan ini milik kosan pemilik yang login
        if ($pemesanan->kosan->user_id !== auth()->id()) {
            abort(403);
        }

        $pemesanan->load(['user', 'kosan.fotoUtama', 'kosan.pemilik']);

        return view('pemilik.pemesanan.show', compact('pemesanan'));
    }

    public function setujui(Pemesanan $pemesanan)
    {
        if ($pemesanan->kosan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Hanya pemesanan berstatus pending yang dapat disetujui.');
        }

        $kosan = $pemesanan->kosan;

        if ($kosan->kamar_tersedia <= 0) {
            return back()->with('error', 'Tidak bisa menyetujui, kamar sudah penuh.');
        }

        $pemesanan->update(['status' => 'disetujui']);
        $kosan->decrement('kamar_tersedia');

        return back()->with('success', 'Pemesanan berhasil disetujui.');
    }

    public function tolak(Pemesanan $pemesanan)
    {
        if ($pemesanan->kosan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Hanya pemesanan berstatus pending yang dapat ditolak.');
        }

        $pemesanan->update(['status' => 'ditolak']);

        return back()->with('success', 'Pemesanan berhasil ditolak.');
    }
}
