<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Development only: auto-login sebagai akun pemilik tanpa form login.
 * Aktif jika APP_ENV=local dan DEV_SKIP_PEMILIK_AUTH=true di .env
 */
class DevLoginAsPemilik
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('local') || ! config('app.dev_skip_pemilik_auth')) {
            abort(403, 'Dev skip auth hanya tersedia di environment local.');
        }

        $pemilik = User::where('role', 'pemilik')->first();

        if (! $pemilik) {
            abort(500, 'User pemilik tidak ditemukan. Jalankan: php artisan db:seed');
        }

        if (! Auth::check() || Auth::user()->role !== 'pemilik') {
            Auth::login($pemilik);
            $request->session()->regenerate();
        }

        return $next($request);
    }
}
