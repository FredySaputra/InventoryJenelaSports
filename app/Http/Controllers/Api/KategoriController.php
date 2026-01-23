<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Bahan;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Resources\KategoriResource;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return KategoriResource::collection($kategoris);
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());
        return new KategoriResource($kategori);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['message' => 'Tidak ditemukan'], 404);
        return new KategoriResource($kategori);
    }

    public function destroy($id)
    {
         $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kategori tidak ditemukan'
            ], 404);
        }


        if ($kategori->produks()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal! Masih ada Produk di kategori ini. Hapus produknya dulu.'
            ], 400);
        }

        // 3. BERSIH-BERSIH (SOLUSI ERROR 500)
        // Kita harus menghapus anak-anaknya (Size & Bahan) dulu sebelum Induknya.
        try {
            // Hapus semua Size yang terhubung ke kategori ini
            $kategori->sizes()->delete();

            // Hapus semua Bahan yang terhubung ke kategori ini
            $kategori->bahans()->delete();

            // 4. Baru Hapus Kategorinya
            $kategori->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus (Data Size & Bahan terkait ikut terhapus)'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // Tangkap error jika masih ada tabel lain yang mengunci (misal di masa depan ada tabel baru)
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal database: Data ini terkunci oleh tabel lain.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server.'
            ], 500);
        }
    }
}
