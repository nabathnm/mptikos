<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_pemilik' => User::where('role', 'pemilik')->count(),
            'total_user' => User::where('role', 'user')->count(),
            'total_kosan' => Kosan::count(),
            'total_pemesanan' => Pemesanan::count(),
            'pending_pemesanan' => Pemesanan::where('status', 'pending')->count(),
            'total_ulasan' => Ulasan::count(),
        ];

        $kosanTerbaru = Kosan::with(['fotoUtama', 'pemilik'])
            ->latest()
            ->take(6)
            ->get();

        $pemesananTerbaru = Pemesanan::with(['user', 'kosan.fotoUtama', 'kosan.pemilik'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'kosanTerbaru', 'pemesananTerbaru'));
    }
}
