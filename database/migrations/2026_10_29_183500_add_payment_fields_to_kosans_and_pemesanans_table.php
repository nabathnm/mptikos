<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kosans', function (Blueprint $table) {
            $table->string('nama_bank')->nullable()->after('status');
            $table->string('nomor_rekening')->nullable()->after('nama_bank');
            $table->string('nama_rekening')->nullable()->after('nomor_rekening');
        });

        Schema::table('pemesanans', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosans', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'nomor_rekening', 'nama_rekening']);
        });

        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });
    }
};

