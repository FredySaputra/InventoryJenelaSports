<?php

namespace App\Http\Controllers;

use App\Models\ProgresProduksi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProduksiController extends Controller
{
    public function konfirmasiPekerjaan(Request $request, $idProgres) {
        // Admin menentukan berapa yang lolos QC (Quality Control)
        $request->validate([
            'jumlah_diterima' => 'required|integer|min:0', // Berapa yang bagus?
            'action' => 'required|in:approve,reject'
        ]);

        return DB::transaction(function () use ($request, $idProgres) {
            $progres = ProgresProduksi::with('detail')->findOrFail($idProgres);

            if ($progres->status !== 'Menunggu') {
                return response()->json(['message' => 'Data ini sudah diproses sebelumnya!'], 400);
            }

            if ($request->action === 'reject') {
                $progres->update(['status' => 'Ditolak']);
                return response()->json(['message' => 'Pekerjaan ditolak.']);
            }

            // JIKA DI-APPROVE (DISINI LOGIKA STOK NYA)

            // 1. Update Status Progres
            $progres->update([
                'status' => 'Disetujui',
                'jumlah_diterima' => $request->jumlah_diterima, // Misal input 20, tapi yg bagus 19
                'waktu_konfirmasi' => now()
            ]);

            // 2. Tambahkan ke Tabel STOK (Increment)
            // Cari stok berdasarkan Produk & Size dari detail job
            $stok = Stok::firstOrCreate(
                [
                    'idProduk' => $progres->detail->idProduk,
                    'idSize'   => $progres->detail->idSize
                ],
                ['stok' => 0]
            );

            // Tambah stok real
            $stok->increment('stok', $request->jumlah_diterima);

            // 3. Update 'jumlah_selesai' di tabel Detail Produksi (Untuk tracking target SPK)
            $progres->detail->increment('jumlah_selesai', $request->jumlah_diterima);

            $detailCurrent = $progres->detail; // Detail yg sedang diproses
            $spkInduk = $detailCurrent->perintahProduksi; // Header SPK

            // 2. Cek apakah SEMUA detail di SPK ini sudah memenuhi target?
            // Kita load ulang semua details milik SPK ini untuk pengecekan
            $allDetails = $spkInduk->details()->get();

            $isAllDone = true;
            foreach($allDetails as $det) {
                if ($det->jumlah_selesai < $det->jumlah_target) {
                    $isAllDone = false;
                    break; // Ada satu yg belum kelar, berarti SPK belum Selesai
                }
            }

            // 3. Jika Semua Done, Update Status SPK jadi 'Selesai'
            if ($isAllDone) {
                $spkInduk->update(['status' => 'Selesai']);
            } else {
                // Jika ada progres masuk, pastikan statusnya 'Proses' (bukan Pending)
                if ($spkInduk->status === 'Pending') {
                    $spkInduk->update(['status' => 'Proses']);
                }
            }

            return response()->json(['message' => 'Sukses! Stok update & Status SPK diperbarui.']);
        });
    }

    // Tambahkan method ini di AdminProduksiController yang sudah Anda buat sebelumnya
    public function getPending()
    {
        // Ambil progres yang statusnya 'Menunggu'
        $data = \App\Models\ProgresProduksi::with(['karyawan', 'detail.produk', 'detail.size'])
            ->where('status', 'Menunggu')
            ->orderBy('waktu_setor', 'asc')
            ->get();

        return response()->json(['data' => $data]);
    }
}
