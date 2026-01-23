<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $table = 'sizes';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function kategori() : BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'idKategori', 'id');
    }

    public function stok() : HasMany
    {
        return $this->hasMany(Stok::class, 'idSize', 'id');
    }

    public function details() :HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'idSize', 'id');
    }
}
