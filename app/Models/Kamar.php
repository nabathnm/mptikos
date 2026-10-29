<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'kosan_id',
        'nama_kamar',
        'harga',
        'fasilitas',
        'status',
    ];

    protected $casts = [
        'harga' => 'integer',
    ];

    public function kosan(): BelongsTo
    {
        return $this->belongsTo(Kosan::class);
    }

    const STATUS_KOSONG = 'kosong';
    const STATUS_TERISI = 'terisi';

    public function isKosong(): bool
    {
        return $this->status === self::STATUS_KOSONG;
    }

    public function isTerisi(): bool
    {
        return $this->status === self::STATUS_TERISI;
    }
}
