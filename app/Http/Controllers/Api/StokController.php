<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Stok; // Pastikan ini ada
use App\Http\Requests\UpdateStokRequest;
use App\Http\Resources\StokResource;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        try {
            // Ambil Kategori, Size, dan Produk
            $kategoris = Kategori::with([
                'sizes' => function($q) {
                    $q->orderBy('id', 'asc');
                },
                'produks' => function($q) {
                    $q->with(['bahan', 'stoks']); // Relasi stoks harus ada di Model Produk
                    $q->orderBy('nama', 'asc');
                }
            ])->orderBy('nama', 'asc')->get();

            $dataMatrix = [];

            foreach ($kategoris as $cat) {
                // KITA HAPUS "CONTINUE" AGAR SEMUA KATEGORI MUNCUL
                // (Membantu debug kategori mana yang kosong)

                $dataMatrix[] = [
                    'kategori_id'   => $cat->id,
                    'kategori_nama' => $cat->nama,
                    'sizes'         => $cat->sizes,
                    'produks'       => $cat->produks->map(function($prod) {

                        // Handle jika bahan null (agar tidak error)
                        $namaBahan = $prod->bahan ? $prod->bahan->nama : '';
                        $warna = $prod->warna ? $prod->warna : '';
                        $namaLengkap = trim($prod->nama . ' ' . $warna . ' ' . $namaBahan);

                        return [
                            'id' => $prod->id,
                            'nama_lengkap' => $namaLengkap,
                            'stoks' => $prod->stoks // Data stok mentah
                        ];
                    })
                ];
            }

            // Pastikan dibungkus 'data'
            return response()->json(['data' => $dataMatrix]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateStokRequest $request)
    {
        $validated = $request->validated();

        $stok = Stok::updateOrCreate(
            [
                'idProduk' => $validated['idProduk'],
                'idSize'   => $validated['idSize']
            ],
            [
                'stok' => $validated['jumlah']
            ]
        );

        return new StokResource($stok);
    }
}
