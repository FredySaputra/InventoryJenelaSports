<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPerintahProduksi extends Model
{
    protected $table = 'detail_perintah_produksis';
    protected $guarded = [];
    public $timestamps = false; // Biasanya detail tidak butuh created_at/updated_at

    // Kembali ke Header SPK
    public function perintahProduksi(): BelongsTo
    {
        return $this->belongsTo(PerintahProduksi::class, 'idPerintahProduksi', 'id');
    }

    // Barang apa yang dibuat?
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'idProduk', 'id');
    }

    // Ukuran apa?
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'idSize', 'id');
    }

    // Siapa saja yang sudah setor kerjaan untuk item ini?
    public function progres(): HasMany
    {
        return $this->hasMany(ProgresProduksi::class, 'idDetailProduksi', 'id');
    }
}
