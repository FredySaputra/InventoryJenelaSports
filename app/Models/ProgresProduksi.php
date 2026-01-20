<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresProduksi extends Model
{
    protected $table = 'progres_produksis';
    protected $guarded = [];
    public $timestamps = false; // Kita pakai custom timestamp (waktu_setor)

    // Agar field tanggal otomatis jadi object Carbon (mudah diformat)
    protected $casts = [
        'waktu_setor' => 'datetime',
        'waktu_konfirmasi' => 'datetime',
    ];

    // Detail pekerjaan mana yang dikerjakan?
    public function detail(): BelongsTo
    {
        return $this->belongsTo(DetailPerintahProduksi::class, 'idDetailProduksi', 'id');
    }

    // Siapa karyawan yang mengerjakan?
    public function karyawan(): BelongsTo
    {
        // Hubungkan ke model User (Tabel users)
        return $this->belongsTo(User::class, 'idKaryawan', 'id');
    }
}
