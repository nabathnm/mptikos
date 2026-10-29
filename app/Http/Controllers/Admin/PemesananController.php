<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;

class PemesananController extends Controller
{
    public function index()
    {
        $pemesanans = Pemesanan::with(['user', 'kosan.fotoUtama', 'kosan.pemilik'])
            ->latest()
            ->paginate(10);

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'kosan.fotoUtama', 'kosan.pemilik']);

        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    public function setujui(Pemesanan $pemesanan)
    {
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
        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Hanya pemesanan berstatus pending yang dapat ditolak.');
        }

        $pemesanan->update(['status' => 'ditolak']);

        return back()->with('success', 'Pemesanan berhasil ditolak.');
    }
}
