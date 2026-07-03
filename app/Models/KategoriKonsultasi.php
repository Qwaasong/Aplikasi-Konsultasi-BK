<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKonsultasi extends Model
{
    protected $table = 'kategori_konsultasi';

    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Satu kategori memiliki banyak konsultasi.
     */
    public function konsultasis()
    {
        return $this->hasMany(Konsultasi::class, 'kategori_id');
    }
}