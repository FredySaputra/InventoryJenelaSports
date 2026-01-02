<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'nama',
        'warna'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'idUser','id');
    }

    public function kategori() : BelongsTo
    {
        return $this->belongsTo(Kategori::class,'idKategori','id');
    }
    public function stok () : HasMany
    {
        return $this->hasMany(Stok::class,'idProduk','id');
    }
}
