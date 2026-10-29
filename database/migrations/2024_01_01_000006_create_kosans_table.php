<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel kosan untuk model Kosan (Set B)
     */
    public function up(): void
    {
        Schema::create('kosans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_kosan');
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->string('kota');
            $table->unsignedInteger('harga_per_bulan');
            $table->unsignedInteger('jumlah_kamar')->default(0);
            $table->unsignedInteger('kamar_tersedia')->default(0);
            $table->enum('tipe', ['putra', 'putri', 'campur'])->default('campur');
            $table->json('fasilitas')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kosans');
    }
};

