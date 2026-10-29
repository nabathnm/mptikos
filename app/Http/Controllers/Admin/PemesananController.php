<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;

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

        return DB::transaction(function () use ($pemesanan) {
            $kosan = Kosan::where('id', $pemesanan->kosan_id)->lockForUpdate()->first();

            if ($kosan->kamar_tersedia <= 0) {
                return back()->with('error', 'Tidak bisa menyetujui, kamar pada kosan ini sudah habis.');
            }

            // 1. Setujui pemesanan terpilih
            $pemesanan->update(['status' => 'disetujui']);
            $kosan->decrement('kamar_tersedia');

            // 2. First-Approved Strategy:
            // Jika kamar habis (kamar_tersedia sekarang 0), tolak semua pemesanan pending lainnya untuk kosan ini secara otomatis
            if ($kosan->kamar_tersedia <= 0) {
                Pemesanan::where('kosan_id', $kosan->id)
                    ->where('status', 'pending')
                    ->where('id', '!=', $pemesanan->id)
                    ->update([
                        'status' => 'ditolak',
                        'catatan' => 'Ditolak otomatis karena kamar telah terisi penuh oleh pemesan lain yang disetujui lebih dahulu.'
                    ]);
            }

            $pesan = 'Pemesanan berhasil disetujui.';
            if ($kosan->kamar_tersedia <= 0) {
                $pesan .= ' Kamar telah penuh, pemesanan pending lainnya otomatis ditolak.';
            }

            return back()->with('success', $pesan);
        });
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
