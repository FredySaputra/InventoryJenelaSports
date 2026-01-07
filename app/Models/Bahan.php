<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    protected $table = 'bahans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'deskripsi',
        'idKategori'
    ];

    public function kategori() : BelongsTo
    {
        return $this->belongsTo(Kategori::class,'idKategori','id');
    }

    public function produk() : BelongsTo
    {
        return $this->belongsTo(Produk::class,'idProduk','id');
    }
}
