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
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'idUser','id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idKategori', 'id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'idBahan', 'id');
    }

    public function stoks()
    {
        return $this->hasMany(Stok::class, 'idProduk', 'id');
    }
}
