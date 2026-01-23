<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgresProduksi;
use Illuminate\Http\Request;

class AdminProduksiController extends Controller
{
    // Fungsi Ambil Data "Menunggu"
    public function getPending()
    {
        try {
            // Ambil data progres yang statusnya Menunggu
            // Load relasi ke karyawan, detail produk, size, dan SPK induknya
            $data = ProgresProduksi::with([
                'karyawan',
                'detail.produk',
                'detail.size',
                'detail.perintahProduksi' // Pastikan relasi ini ada di model DetailPerintahProduksi
            ])
                ->where('status', 'Menunggu')
                ->orderBy('waktu_setor', 'asc') // Yang setor duluan di atas
                ->get();

            // Format data agar mudah dibaca frontend
            $formatted = $data->map(function($item) {
                // Cek null safety (jaga-jaga ada data yatim)
                $produk = $item->detail->produk ?? null;
                $spk = $item->detail->perintahProduksi ?? null;

                return [
                    'id_progres'   => $item->id,
                    'waktu'        => date('d M Y H:i', strtotime($item->waktu_setor)),
                    'karyawan'     => $item->karyawan->nama ?? 'Unknown',
                    'no_spk'       => $spk ? $spk->id : '-',
                    'produk'       => $produk ? $produk->nama . ' ' . ($produk->warna ?? '') : '-',
                    'size'         => $item->detail->size->tipe ?? '-',
                    'jumlah_setor' => $item->jumlah_disetor,
                    'id_detail'    => $item->idDetailProduk // Untuk update stok nanti
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formatted
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
