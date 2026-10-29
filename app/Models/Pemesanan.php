<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemesanan extends Model
{
    protected $fillable = [
        'user_id',
        'kosan_id',
        'tanggal_masuk',
        'durasi_bulan',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'durasi_bulan' => 'integer',
        'total_harga' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kosan(): BelongsTo
    {
        return $this->belongsTo(Kosan::class);
    }
}
