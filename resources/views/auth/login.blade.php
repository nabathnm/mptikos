<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — EasyKos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <div class="nb-shell min-h-screen flex items-center py-10">
        <section class="nb-card mx-auto w-full max-w-xl p-6 md:p-8">
            <a href="{{ url('/') }}" class="nb-kicker hover:underline">Kembali ke Landing</a>
            <h1 class="mt-2 text-5xl font-black leading-none">Masuk</h1>
            <p class="mt-2 text-xl font-semibold">Akses dashboard dan data kosan.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label class="nb-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="nb-input" placeholder="nama@email.com">
                    @error('email')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="nb-label">Password</label>
                    <input type="password" name="password" required class="nb-input" placeholder="••••••••">
                    @error('password')<p class="nb-error">{{ $message }}</p>@enderror
                </div>

                <label class="inline-flex items-center gap-2 text-lg font-semibold">
                    <input type="checkbox" name="remember" class="h-5 w-5 border-2 border-black">
                    Ingat saya
                </label>

                <button type="submit" class="nb-btn nb-btn-primary w-full text-xl">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-lg font-medium">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-black underline">Daftar</a>
            </p>
        </section>
    </div>
</body>
</html>
