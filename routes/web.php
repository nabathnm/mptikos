<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pemilik\KosanController as PemilikKosanController;
use App\Http\Controllers\Pemilik\PemesananController as PemilikPemesananController;
use App\Http\Controllers\User\KosanController as UserKosanController;
use App\Http\Controllers\User\PemesananController as UserPemesananController;

// Halaman utama (landing) untuk tamu maupun user yang sudah login
Route::get('/', function () {
    return view('auth.choose');
})->name('welcome');

// Beranda publik (daftar kosan)
Route::get('/home', [UserKosanController::class, 'index'])->name('home');
Route::get('/kosan', [UserKosanController::class, 'search'])->name('kosan.search');
Route::get('/kosan/{kosan}', [UserKosanController::class, 'show'])->name('kosan.show');

// Dashboard umum (redirect sesuai role: pemilik atau user)
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'pemilik') {
        return redirect()->route('pemilik.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Pemilik Kosan (development: set DEV_SKIP_PEMILIK_AUTH=true di .env)
$pemilikMiddleware = (app()->environment('local') && config('app.dev_skip_pemilik_auth'))
    ? ['dev.pemilik']
    : ['auth', 'role:pemilik'];

Route::middleware($pemilikMiddleware)->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', [PemilikKosanController::class, 'dashboard'])->name('dashboard');
    Route::resource('kosan', PemilikKosanController::class);
    Route::resource('pemesanan', PemilikPemesananController::class)->only(['index', 'show']);
    Route::patch('pemesanan/{pemesanan}/setujui', [PemilikPemesananController::class, 'setujui'])->name('pemesanan.setujui');
    Route::patch('pemesanan/{pemesanan}/tolak', [PemilikPemesananController::class, 'tolak'])->name('pemesanan.tolak');
});

// User / Pencari Kosan
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserPemesananController::class, 'dashboard'])->name('dashboard');
    Route::resource('pemesanan', UserPemesananController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('pemesanan/{pemesanan}/bukti', [UserPemesananController::class, 'uploadBukti'])->name('pemesanan.upload_bukti');
    Route::post('/kosan/{kosan}/ulasan', [UserKosanController::class, 'ulasan'])->name('kosan.ulasan');
});

require __DIR__.'/auth.php';
