<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;

class UlasanController extends Controller
{
    public function index()
    {
        $ulasans = Ulasan::with(['user', 'kosan'])
            ->latest()
            ->paginate(12);

        return view('admin.ulasan.index', compact('ulasans'));
    }

    public function destroy(Ulasan $ulasan)
    {
        $ulasan->delete();

        return redirect()->route('admin.ulasan.index')->with('success', 'Ulasan berhasil dihapus.');
    }
}
