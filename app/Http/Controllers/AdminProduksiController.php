<?php

namespace App\Http\Controllers;

use App\Models\ProgresProduksi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProduksiController extends Controller
{
    public function konfirmasiPekerjaan(Request $request, $idProgres)
    {
        // 1. Cari data dulu
        $progres = ProgresProduksi::with('detail')->find($idProgres);

        if (!$progres) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // 2. Cek Status (Gunakan strtolower agar tidak sensitif huruf besar/kecil)
        // Trim untuk membuang spasi yang tidak sengaja terbawa di database
        if (trim(strtolower($progres->status)) !== 'menunggu') {
            return response()->json([
                'message' => 'Gagal: Data ini statusnya sudah bukan Menunggu (Status: ' . $progres->status . ')'
            ], 400);
        }

        return DB::transaction(function () use ($request, $progres) {

            // --- SKENARIO 1: TOLAK (REJECT) ---
            if ($request->action === 'reject') {
                // Langsung update status jadi Ditolak tanpa validasi jumlah
                $progres->update([
                    'status' => 'Ditolak',
                    'waktu_konfirmasi' => now()
                ]);
                return response()->json(['message' => 'Laporan berhasil ditolak.']);
            }

            // --- SKENARIO 2: TERIMA (APPROVE) ---
            // Baru kita validasi jumlah jika aksinya approve
            $request->validate([
                'jumlah_diterima' => 'required|integer|min:0'
            ]);

            // Update Status Progres
            $progres->update([
                'status' => 'Disetujui',
                'jumlah_diterima' => $request->jumlah_diterima,
                'waktu_konfirmasi' => now()
            ]);

            // Tambahkan ke Tabel STOK (Increment)
            $stok = Stok::firstOrCreate(
                [
                    'idProduk' => $progres->detail->idProduk, // ID String (KRT-TB)
                    'idSize'   => $progres->detail->idSize    // ID String (BJU-S)
                ],
                ['stok' => 0]
            );

            $stok->increment('stok', $request->jumlah_diterima);

            // Update target SPK
            $progres->detail->increment('jumlah_selesai', $request->jumlah_diterima);

            // Cek apakah SPK Selesai (Logic update status SPK)
            $detailCurrent = $progres->detail;
            $spkInduk = $detailCurrent->perintahProduksi;

            if ($spkInduk) {
                $allDetails = $spkInduk->details()->get();
                $isAllDone = true;
                foreach($allDetails as $det) {
                    if ($det->jumlah_selesai < $det->jumlah_target) {
                        $isAllDone = false;
                        break;
                    }
                }

                if ($isAllDone) {
                    $spkInduk->update(['status' => 'Selesai']);
                } else {
                    if ($spkInduk->status === 'Pending') {
                        $spkInduk->update(['status' => 'Proses']);
                    }
                }
            }

            return response()->json(['message' => 'Sukses! Stok update & Status SPK diperbarui.']);
        });
    }

    // Tambahkan method ini di AdminProduksiController yang sudah Anda buat sebelumnya
    public function getPending()
    {
        $data = \App\Models\ProgresProduksi::with(['karyawan', 'detail.produk', 'detail.size'])
            ->where('status', 'Menunggu') // <--- WAJIB ADA! Agar data Ditolak/Disetujui hilang dari tabel.
            ->orderBy('waktu_setor', 'asc')
            ->get();

        $formatted = $data->map(function($item) {
            return [
                'id_progres'   => $item->id,
                'waktu'        => $item->waktu_setor,
                'karyawan'     => $item->karyawan->nama ?? 'Unknown',
                'no_spk'       => $item->detail->perintahProduksi->id ?? '-',
                'produk'       => $item->detail->produk->nama ?? '-', // Kalau ini masih -, lakukan Perbaikan 2
                'size'         => $item->detail->size->tipe ?? '-',
                'jumlah_setor' => $item->jumlah_disetor,
                'id_detail'    => $item->idDetailProduksi
            ];
        });

        return response()->json(['data' => $formatted]);
    }
}
