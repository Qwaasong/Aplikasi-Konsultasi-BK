<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriKasus extends Model
{
    use HasFactory;

    protected $table = 'kategori_kasus';

    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Satu kategori memiliki banyak kasus BK.
     */
    public function kasus(): HasMany
    {
        return $this->hasMany(KasusBk::class, 'kategori_id');
    }
}
