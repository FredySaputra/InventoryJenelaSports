<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $namaBahan = $this->relationLoaded('bahan') && $this->bahan ? $this->bahan->nama : '';

        $warna = $this->warna ?? '';

        $namaLengkap = trim($this->nama . ' ' . $warna . ' ' . $namaBahan);

        return [
            'id' => $this->id,
            'nama' => $this->nama,

            'nama_lengkap' => $namaLengkap,

            'warna' => $this->warna,
            'idKategori' => $this->idKategori,
            'kategori_nama' => $this->kategori ? $this->kategori->nama : null,

            'bahan_id' => $this->idBahan,
            'bahan_nama' => $namaBahan,
            'stoks' => $this->whenLoaded('stoks'),
        ];
    }
}
