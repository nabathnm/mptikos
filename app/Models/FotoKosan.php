<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoKosan extends Model
{
    protected $fillable = [
        'kosan_id',
        'foto',
        'is_utama',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    protected $appends = [
        'url',
    ];

    public function kosan(): BelongsTo
    {
        return $this->belongsTo(Kosan::class);
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->foto)) {
            return '';
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        return asset('storage/' . $this->foto);
    }
}
