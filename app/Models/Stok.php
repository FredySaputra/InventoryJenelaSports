<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stok extends Model
{
    protected $table = 'stoks';

    public $timestamps = false;
    protected $guarded = [];

    public function produk() : BelongsTo
    {
        return $this->belongsTo(Produk::class,'idProduk','id');
    }

    public function size() : BelongsTo
    {
        return $this->belongsTo(Size::class,'idSize','id');
    }
}
