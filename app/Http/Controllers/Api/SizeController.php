<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\Kategori;
use App\Http\Resources\SizeResource;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;


class SizeController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::with(['sizes' => function($query) {
            $query->orderBy('id', 'asc');
        }])->orderBy('nama', 'asc')->get();

        $groupedData = $kategoris->map(function ($cat) {
            return [
                'kategori_id'   => $cat->id,
                'kategori_nama' => $cat->nama,
                'sizes'         => SizeResource::collection($cat->sizes)
            ];
        });

        $orphans = Size::whereNull('idKategori')->get();
        if ($orphans->count() > 0) {
            $groupedData->push([
                'kategori_id'   => null,
                'kategori_nama' => 'Tanpa Kategori / Lain-lain',
                'sizes'         => SizeResource::collection($orphans)
            ]);
        }

        return response()->json(['data' => $groupedData]);
    }

    public function store(StoreSizeRequest $request) {
        $size = Size::create($request->validated());
        return new SizeResource($size);
    }

    public function show($id) {
        $size = Size::find($id);
        if (!$size) return response()->json(['message' => 'Not Found'], 404);
        return new SizeResource($size);
    }

    public function update(UpdateSizeRequest $request, $id) {
        $size = Size::find($id);
        if (!$size) return response()->json(['message' => 'Not Found'], 404);
        $size->update($request->validated());
        return new SizeResource($size);
    }

    public function destroy($id) {
        try {
            $size = Size::find($id);
            if (!$size) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Coba hapus
            $size->delete();

            return response()->json(['message' => 'Berhasil dihapus']);

        } catch (\Illuminate\Database\QueryException $e) {
            // Error 23000 adalah kode SQL untuk Integrity Constraint Violation (Data terpaut)
            if ($e->getCode() == "23000") {
                return response()->json([
                    'message' => 'Gagal menghapus: Size ini sedang digunakan di Data Stok atau SPK. Hapus dulu data yang terkait.'
                ], 409); // 409 Conflict
            }

            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
