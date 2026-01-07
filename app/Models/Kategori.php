<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategoris';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nama'
    ];

    public function produks() : HasMany
    {
        return $this->hasMany(Produk::class, 'idKategori', 'id');
    }

    public function bahan() : HasMany
    {
        return $this->hasMany(Bahan::class, 'idKategori', 'id');
    }

    public function sizes() : HasMany
    {
        return $this->hasMany(Size::class, 'idKategori', 'id');
    }
}
