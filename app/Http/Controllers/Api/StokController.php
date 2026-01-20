<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Stok; // Pastikan ini ada
use App\Http\Requests\UpdateStokRequest;
use App\Http\Resources\StokResource;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

    public function exportPdf()
    {
        try {
            // 1. Ambil Data Tanggal Update Terakhir
            $lastUpdate = Stok::max('updated_at');
            $tanggalUpdate = $lastUpdate ? Carbon::parse($lastUpdate)->translatedFormat('d F Y H:i') : '-';

            // 2. Ambil Data Matrix (Sama seperti index, tapi get() langsung untuk View)
            $kategoris = Kategori::with([
                'sizes' => function($q) { $q->orderBy('id', 'asc'); },
                'produks' => function($q) {
                    $q->with(['bahan', 'stoks']);
                    $q->orderBy('nama', 'asc');
                }
            ])->orderBy('nama', 'asc')->get();

            // 3. Generate PDF
            $pdf = Pdf::loadView('pdf.laporan_stok', [
                'kategoris' => $kategoris,
                'tanggalUpdate' => $tanggalUpdate
            ]);

            // Set ukuran kertas (A4 Landscape agar muat banyak kolom)
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download('Laporan_Stok_Jenela_Sports.pdf');

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal cetak PDF: ' . $e->getMessage()], 500);
        }
    }
}
