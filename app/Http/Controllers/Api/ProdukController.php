<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Produk;
use App\Models\Kategori;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Resources\ProdukResource;
use App\Http\Resources\KategoriWithProdukResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::with(['produks' => function($query) {
            $query->with('bahan');
            $query->orderBy('nama', 'asc');
        }])->orderBy('nama', 'asc')->get();

        return KategoriWithProdukResource::collection($kategoris);
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();
        $data['idUser'] = $request->user()->id;

        $produk = Produk::create($data);
        $produk->load(['kategori', 'bahan']);

        return new ProdukResource($produk);
    }

    public function show($id)
    {
        $produk = Produk::with(['kategori', 'bahan', 'stoks'])->find($id);
        if (!$produk) return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        return new ProdukResource($produk);
    }

    public function update(UpdateProdukRequest $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Produk tidak ditemukan'], 404);

        $produk->update($request->validated());
        $produk->load(['kategori', 'bahan']);

        return new ProdukResource($produk);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $cekTransaksi = DB::table('detail_transaksis')
            ->where('idProduk', $id)
            ->count();

        if ($cekTransaksi > 0) {
            return response()->json([
                'message' => 'Gagal menghapus! Produk ini sudah memiliki riwayat transaksi penjualan. Data tidak boleh dihapus demi arsip keuangan.'
            ], 400);
        }
        $produk->stoks()->delete();
        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
