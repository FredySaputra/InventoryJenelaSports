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
        $size = Size::find($id);
        if (!$size) return response()->json(['message' => 'Not Found'], 404);
        $size->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
}
