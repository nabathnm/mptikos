<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F3F4F6] flex items-center justify-center min-h-screen p-6">
    <div class="nb-card p-10 max-w-lg text-center border-[#EF4444]">
        <h1 class="text-9xl font-black mb-4 text-[#EF4444]">403</h1>
        <p class="text-3xl font-black mb-6">Eits! Nggak Boleh Masuk.</p>
        <p class="text-xl font-medium mb-8 text-gray-600">Kamu nggak punya izin buat ngintip halaman ini, Bos.</p>
        <a href="{{ url('/') }}" class="nb-btn nb-btn-primary px-8 py-3 text-xl inline-block">
            Balik ke Beranda
        </a>
    </div>
</body>
</html>
