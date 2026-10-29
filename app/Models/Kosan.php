<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kosan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kosan',
        'alamat',
        'deskripsi',
        'kota',
        'harga_per_bulan',
        'jumlah_kamar',
        'kamar_tersedia',
        'tipe',
        'fasilitas',
        'status',
        'nama_bank',
        'nomor_rekening',
        'nama_rekening',
    ];

    protected $casts = [
        'fasilitas' => 'array',
    ];

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->pemilik();
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoKosan::class);
    }

    public function fotoUtama(): HasOne
    {
        return $this->hasOne(FotoKosan::class)->where('is_utama', true);
    }

    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function ulasans(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }

    public function getRekeningBankAttribute(): ?string
    {
        return $this->nama_bank ?: ($this->pemilik->nama_bank ?? null);
    }

    public function getRekeningNomorAttribute(): ?string
    {
        return $this->nomor_rekening ?: ($this->pemilik->nomor_rekening ?? null);
    }

    public function getRekeningNamaAttribute(): ?string
    {
        return $this->nama_rekening ?: ($this->pemilik->nama_rekening ?? ($this->pemilik->nama ?? null));
    }
}
