<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoKos extends Model
{
    protected $table = 'foto_kos';

    protected $fillable = [
        'kos_id',
        'url_foto',
        'urutan',
    ];

    // Relationships
    public function kos()
    {
        return $this->belongsTo(Kos::class, 'kos_id');
    }
}
