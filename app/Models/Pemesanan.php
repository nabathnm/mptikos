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
        'bukti_pembayaran',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'durasi_bulan' => 'integer',
        'total_harga' => 'integer',
    ];

    protected $appends = [
        'bukti_pembayaran_url',
    ];

    public function getBuktiPembayaranUrlAttribute(): ?string
    {
        if (empty($this->bukti_pembayaran)) {
            return null;
        }

        if (str_starts_with($this->bukti_pembayaran, 'http://') || str_starts_with($this->bukti_pembayaran, 'https://')) {
            return $this->bukti_pembayaran;
        }

        return asset('storage/' . $this->bukti_pembayaran);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kosan(): BelongsTo
    {
        return $this->belongsTo(Kosan::class);
    }
}
