<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProduksiKaryawanController extends Controller
{
    public function setorPekerjaan(Request $request) {
        // 1. Karyawan input: "Saya ngerjain Job ID sekian, 20 pcs"
        $data = $request->validate([
            'id_detail_produksi' => 'required|exists:detail_perintah_produksis,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $progres = ProgresProduksi::create([
            'idDetailProduksi' => $data['id_detail_produksi'],
            'idKaryawan' => auth()->id(), // Ambil ID user dari Token JWT
            'jumlah_disetor' => $data['jumlah'],
            'status' => 'Menunggu', // Default menunggu Admin cek
            'waktu_setor' => now()
        ]);

        return response()->json(['message' => 'Laporan terkirim! Tunggu konfirmasi Admin.']);
    }
}
