<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ulasan/review untuk model Ulasan (Set B)
     */
    public function up(): void
    {
        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kosan_id')->constrained('kosans')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // skala 1-5
            $table->text('komentar')->nullable();
            $table->timestamps();

            // Satu user hanya bisa memberi satu ulasan per kosan
            $table->unique(['user_id', 'kosan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};

