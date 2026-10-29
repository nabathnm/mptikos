<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F3F4F6] flex items-center justify-center min-h-screen p-6">
    <div class="nb-card p-10 max-w-lg text-center">
        <h1 class="text-9xl font-black mb-4">404</h1>
        <p class="text-3xl font-black mb-6">Waduh! Kesasar ya?</p>
        <p class="text-xl font-medium mb-8 text-gray-600">Halaman yang kamu cari nggak ada di sini.</p>
        <a href="{{ url('/') }}" class="nb-btn nb-btn-primary px-8 py-3 text-xl inline-block">
            Balik ke Beranda
        </a>
    </div>
</body>
</html>
