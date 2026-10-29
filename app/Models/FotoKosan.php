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

    public function kosan(): BelongsTo
    {
        return $this->belongsTo(Kosan::class);
    }
}
