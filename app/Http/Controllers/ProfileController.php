<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Kosan;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $stats = [];

        if ($user->role === 'pemilik') {
            $kosanIds = Kosan::where('user_id', $user->id)->pluck('id');
            $stats = [
                'total_kosan' => $kosanIds->count(),
                'kamar_tersedia' => Kosan::whereIn('id', $kosanIds)->sum('kamar_tersedia'),
                'total_pemesanan' => Pemesanan::whereIn('kosan_id', $kosanIds)->count(),
                'pending_pemesanan' => Pemesanan::whereIn('kosan_id', $kosanIds)->where('status', 'pending')->count(),
            ];
        } else {
            $stats = [
                'total_pemesanan' => Pemesanan::where('user_id', $user->id)->count(),
                'pending_pemesanan' => Pemesanan::where('user_id', $user->id)->where('status', 'pending')->count(),
                'disetujui_pemesanan' => Pemesanan::where('user_id', $user->id)->where('status', 'disetujui')->count(),
                'ditolak_pemesanan' => Pemesanan::where('user_id', $user->id)->where('status', 'ditolak')->count(),
            ];
        }

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
