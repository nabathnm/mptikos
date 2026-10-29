<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel foto untuk model Kosan (Set B)
     */
    public function up(): void
    {
        Schema::create('foto_kosans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kosan_id')->constrained('kosans')->cascadeOnDelete();
            $table->string('foto');
            $table->boolean('is_utama')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_kosans');
    }
};

