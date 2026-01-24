<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerintahProduksi;
use App\Models\DetailPerintahProduksi;
use App\Models\ProgresProduksi;
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

    public function getSpkActive()
    {
        $data = PerintahProduksi::with(['pelanggan', 'details.produk.bahan', 'details.size', 'details.progres'])
            ->whereIn('status', ['Pending', 'Proses'])
            ->orderBy('tanggal_target', 'asc')
            ->get();

        $formatted = $data->map(function($spk) {
            return [
                'id_spk' => $spk->id,
                'target_date' => $spk->tanggal_target,
                'pelanggan' => $spk->pelanggan ? $spk->pelanggan->nama : 'Stok Gudang',
                'catatan' => $spk->catatan,

                'items' => $spk->details->map(function($detail) {
                    $resmiSelesai = $detail->progres
                        ->where('status', 'Disetujui')
                        ->sum('jumlah_diterima');

                    $sedangOtw = $detail->progres
                        ->where('status', 'Menunggu')
                        ->sum('jumlah_disetor');

                    $totalProgress = $resmiSelesai + $sedangOtw;

                    $sisaQty = $detail->jumlah_target - $totalProgress;
                    if ($sisaQty < 0) $sisaQty = 0;

                    $labelSisa = ($sisaQty === 0) ? "Selesai" : "{$sisaQty} Pcs";

                    $namaBahan = $detail->produk->bahan ? $detail->produk->bahan->nama : '';
                    $warna = $detail->produk->warna ? $detail->produk->warna : '';
                    $namaProduk = trim($detail->produk->nama . ' ' . $warna . ' ' . $namaBahan);

                    return [
                        'id_detail' => $detail->id,
                        'produk' => $namaProduk,
                        'size' => $detail->size->tipe,
                        'target' => $detail->jumlah_target,

                        'progress_total' => $totalProgress,
                        'sisa_qty' => $sisaQty,

                        'sisa_label' => $labelSisa
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

    public function storeProgress(Request $request)
    {
        // 1. Validasi Input
        $validator = \Validator::make($request->all(), [
            'id_detail' => 'required|exists:detail_perintah_produksis,id', // ID Barang yg dikerjakan
            'jumlah'    => 'required|integer|min:1', // Jumlah yg disetor
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            // 2. Ambil User yang sedang Login (Karyawan)
            $user = auth()->user();

            // 3. Simpan ke Tabel Progres Produksi
            // Sesuai struktur tabel di gambar database Anda
            $progres = ProgresProduksi::create([
                'idDetailProduksi' => $request->id_detail,
                'idKaryawan'       => $user->id,
                'jumlah_disetor'   => $request->jumlah,
                'jumlah_diterima'  => 0, // Default 0 sebelum dicek admin
                'status'           => 'Menunggu', // PENTING: Status awal Menunggu
                'waktu_setor'      => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim! Menunggu verifikasi admin.',
                'data'    => $progres
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeDirectInput(Request $request)
    {
        // 1. Validasi Input dari Android
        $validator = \Validator::make($request->all(), [
            'id_produk' => 'required|exists:produks,id',
            'id_size'   => 'required|exists:sizes,id',
            'jumlah'    => 'required|integer|min:1',
            'tanggal'   => 'required|date', // Tanggal dari inputan Android
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $idSpkKhusus = 'SPK-DIRECT';

            $detail = \App\Models\DetailPerintahProduksi::firstOrCreate(
                [
                    'idPerintahProduksi' => $idSpkKhusus,
                    'idProduk'           => $request->id_produk,
                    'idSize'             => $request->id_size,
                ],
                [
                    'jumlah_target'  => 0, // Target 0 karena ini bukan target kerjaan
                    'jumlah_selesai' => 0
                ]
            );

            // 3. Simpan ke Progres Produksi (Agar masuk ke Verifikasi Admin)
            $progres = \App\Models\ProgresProduksi::create([
                'idDetailProduksi' => $detail->id,
                'idKaryawan'       => $user->id,
                'jumlah_disetor'   => $request->jumlah,
                'jumlah_diterima'  => 0,
                'status'           => 'Menunggu', // PENTING: Agar muncul di Admin
                'waktu_setor'      => $request->tanggal . ' ' . now()->format('H:i:s'), // Gabung tanggal input + jam sekarang
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim ke Admin untuk diverifikasi!',
                'data'    => $progres
            ]);
        });
    }

    // Endpoint untuk List Produk di Android
    public function getProdukForAndroid()
    {
        // Ambil produk beserta relasi bahannya
        $produks = \App\Models\Produk::with('bahan')->get();

        // Format datanya agar nama sudah digabung
        $formatted = $produks->map(function($item) {
            $namaBahan = $item->bahan ? $item->bahan->nama : ''; // Handle jika bahan null
            $warna = $item->warna ? $item->warna : '';           // Handle jika warna null

            // Gabung string: "Baju Karate" + "Merah" + "Drill"
            // Gunakan trim agar tidak ada spasi berlebih jika ada yang kosong
            $namaLengkap = trim($item->nama . ' ' . $warna . ' ' . $namaBahan);

            return [
                'id' => $item->id,
                'nama_tampil' => $namaLengkap // Ini yang akan tampil di Android
            ];
        });

        return response()->json($formatted);
    }

// Endpoint untuk Get Size berdasarkan ID Produk yang dipilih
    public function getSizeByProduk($idProduk)
    {
        // 1. Cari dulu produknya untuk tahu dia masuk Kategori apa
        $produk = \App\Models\Produk::find($idProduk);

        if (!$produk) {
            return response()->json([]);
        }

        // 2. Ambil Size yang idKategori-nya sama dengan produk tersebut
        // Contoh: Jika pilih Baju, hanya muncul S, M, L, XL
        // Jika pilih Sabuk, hanya muncul Kecil, Standar (Sesuai database Anda)
        $sizes = \App\Models\Size::where('idKategori', $produk->idKategori)
            ->orderBy('id', 'asc') // Atau urutkan custom jika perlu
            ->get(['id', 'tipe']); // Ambil ID dan Tipe (Label)

        return response()->json($sizes);
    }


}
