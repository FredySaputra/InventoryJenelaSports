<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerintahProduksi extends Model
{
    protected $table = 'perintah_produksis';

    // Konfigurasi Primary Key Custom (String: SPK-XXX)
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'idPelanggan', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPerintahProduksi::class, 'idPerintahProduksi', 'id');
    }
}
