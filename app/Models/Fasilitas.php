<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';

    protected $fillable = [
        'nama_fasilitas',
    ];

    public static function listForForm(): array
    {
        $items = self::orderBy('nama_fasilitas')->pluck('nama_fasilitas')->toArray();

        if (! empty($items)) {
            return $items;
        }

        return self::defaultList();
    }

    public static function defaultList(): array
    {
        return [
            'WiFi',
            'AC',
            'Kamar Mandi Dalam',
            'Kamar Mandi Luar',
            'Dapur',
            'Parkir Motor',
            'Parkir Mobil',
            'Lemari',
            'Kasur',
            'Meja Belajar',
            'TV',
            'Laundry',
        ];
    }

    // Relationships
    public function kos()
    {
        return $this->belongsToMany(Kos::class, 'kos_fasilitas', 'fasilitas_id', 'kos_id');
    }
}
