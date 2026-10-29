<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'role'           => ['required', 'in:user,pemilik'],
            'no_telepon'     => ['nullable', 'string', 'max:30'],
            'nama_bank'      => ['nullable', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'nama_rekening'  => ['nullable', 'string', 'max:150'],
        ]);

        $role = $request->role === 'pemilik' ? 'pemilik' : 'user';

        $user = User::create([
            'nama'           => $request->nama,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $role,
            'no_telepon'     => $request->no_telepon,
            'nama_bank'      => $role === 'pemilik' ? $request->nama_bank : null,
            'nomor_rekening' => $role === 'pemilik' ? $request->nomor_rekening : null,
            'nama_rekening'  => $role === 'pemilik' ? ($request->nama_rekening ?: $request->nama) : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect sesuai role
        if ($user->role === 'pemilik') {
            return redirect()->route('pemilik.dashboard');
        }

        return redirect()->route('home');
    }
}
