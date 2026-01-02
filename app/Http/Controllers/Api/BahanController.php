<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Http\Requests\StoreBahanRequest;
use App\Http\Resources\BahanResource;

class BahanController extends Controller
{
    public function index()
    {
        $bahans = Bahan::orderBy('nama')->get();
        return BahanResource::collection($bahans);
    }

    public function getByKategori($idKategori)
    {
        $bahans = Bahan::where('idKategori', $idKategori)
            ->orderBy('nama')
            ->get();

        return BahanResource::collection($bahans);
    }

    public function store(StoreBahanRequest $request)
    {
        $bahan = Bahan::create($request->validated());
        return new BahanResource($bahan);
    }

    public function destroy($id)
    {
        $bahan = Bahan::find($id);

        if (!$bahan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $bahan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bahan berhasil dihapus'
        ]);
    }
}
