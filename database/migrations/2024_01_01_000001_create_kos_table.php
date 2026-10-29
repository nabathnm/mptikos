<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk model Kos (Set A)
     */
    public function up(): void
    {
        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilik_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_kos');
            $table->text('alamat');
            $table->string('kota');
            $table->string('kecamatan')->nullable();
            $table->enum('tipe_kos', ['putra', 'putri', 'campur'])->default('campur');
            $table->decimal('harga', 12, 2);
            $table->enum('periode_harga', ['bulan', 'tahun'])->default('bulan');
            $table->unsignedInteger('jumlah_kamar')->default(0);
            $table->enum('status_kamar', ['tersedia', 'penuh'])->default('tersedia');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};

