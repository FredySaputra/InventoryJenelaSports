<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Bahan; // Kita butuh ini untuk cek relasi saat hapus
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * GET /api/kategoris
     * Mengambil semua kategori
     */
    public function index()
    {
        return response()->json(Kategori::orderBy('nama')->get());
    }

    /**
     * POST /api/kategoris
     * Menambah kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:100|unique:kategoris,id',
            'nama' => 'required|string|max:255'
        ]);

        Kategori::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil disimpan'
        ], 201);
    }

    /**
     * GET /api/kategoris/{id}
     * Detail 1 kategori
     */
    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['message' => 'Tidak ditemukan'], 404);
        return response()->json($kategori);
    }

    /**
     * DELETE /api/kategoris/{id}
     * Hapus kategori (Hanya jika tidak ada bahan di dalamnya)
     */
    public function destroy($id)
    {
        // Cek apakah kategori ini dipakai di tabel bahan
        if (Bahan::where('idKategori', $id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal! Kategori ini masih memiliki bahan. Hapus bahannya dulu.'
            ], 400);
        }

        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['message' => 'Tidak ditemukan'], 404);

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
