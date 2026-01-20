<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerintahProduksi;
use App\Models\DetailPerintahProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerintahProduksiController extends Controller
{
    public function index()
    {
        // Ambil data SPK beserta Pelanggan dan total item
        $data = PerintahProduksi::with(['pelanggan', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'items' => 'required|array', // Array produk
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Buat ID Unik (SPK-YYYYMMDD-XXX)
            $count = PerintahProduksi::whereDate('created_at', now())->count() + 1;
            $idSpk = 'SPK-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header
            $spk = PerintahProduksi::create([
                'id' => $idSpk,
                'tanggal_mulai' => now(),
                'tanggal_target' => $request->tanggal_target,
                'idPelanggan' => $request->idPelanggan, // Bisa null jika stok gudang
                'status' => 'Pending',
                'catatan' => $request->catatan
            ]);

            // 3. Simpan Detail Item
            foreach ($request->items as $item) {
                DetailPerintahProduksi::create([
                    'idPerintahProduksi' => $idSpk,
                    'idProduk' => $item['idProduk'],
                    'idSize' => $item['idSize'],
                    'jumlah_target' => $item['jumlah_target'],
                    'jumlah_selesai' => 0
                ]);
            }

            return response()->json(['message' => 'SPK Berhasil Dibuat', 'id' => $idSpk]);
        });
    }

    // Di dalam class PerintahProduksiController

    public function getSpkActive()
    {
        // Ambil SPK yang statusnya "Proses" saja (yang "Selesai" tidak perlu muncul di HP)
        $data = PerintahProduksi::with(['details.produk', 'details.size'])
            ->whereIn('status', ['Pending', 'Proses'])
            ->orderBy('tanggal_target', 'asc') // Urutkan deadline terdekat
            ->get();

        // Kita rapikan datanya agar Android mudah membacanya
        $formatted = $data->map(function($spk) {
            return [
                'id_spk' => $spk->id,
                'target_date' => $spk->tanggal_target,
                'catatan' => $spk->catatan,
                // List Barang dalam SPK tersebut
                'items' => $spk->details->map(function($detail) {
                    return [
                        'id_detail' => $detail->id, // PENTING: Ini ID untuk setor kerjaan nanti
                        'produk' => $detail->produk->nama . ' ' . ($detail->produk->warna ?? ''),
                        'size' => $detail->size->tipe, // Pastikan nama kolom size benar
                        'target' => $detail->jumlah_target,
                        'selesai' => $detail->jumlah_selesai,
                        'sisa' => $detail->jumlah_target - $detail->jumlah_selesai
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function show($id)
    {
        $spk = PerintahProduksi::with(['pelanggan', 'details.produk', 'details.size'])
            ->where('id', $id)
            ->first();

        if (!$spk) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['data' => $spk]);
    }

    public function batalkan($id)
    {
        $spk = PerintahProduksi::findOrFail($id);

        if ($spk->status === 'Selesai') {
            return response()->json(['message' => 'SPK sudah Selesai, tidak bisa dibatalkan!'], 400);
        }

        $spk->update(['status' => 'Dibatalkan']);

        return response()->json(['message' => 'SPK Berhasil Dibatalkan']);
    }
}
