<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    protected $table = 'kos';

    protected $fillable = [
        'pemilik_id',
        'nama_kos',
        'alamat',
        'kota',
        'kecamatan',
        'tipe_kos',
        'harga',
        'periode_harga',
        'jumlah_kamar',
        'status_kamar',
        'deskripsi',
    ];

    // Relationships
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoKos::class, 'kos_id');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'kos_fasilitas', 'kos_id', 'fasilitas_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'kos_id');
    }
}
