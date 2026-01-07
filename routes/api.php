<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PelanggansController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BahanController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\StokController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\KaryawanController;

Route::post('/users/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/kategoris', [KategoriController::class, 'index']);
    Route::post('/kategoris', [KategoriController::class, 'store']);
    Route::get('/kategoris/{id}', [KategoriController::class, 'show']);
    Route::put('/kategoris/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategoris/{id}', [KategoriController::class, 'destroy']);

    Route::get('/bahans/kategori/{idKategori}', [BahanController::class, 'getByKategori']);
    Route::get('/bahans', [BahanController::class, 'index']);
    Route::post('/bahans', [BahanController::class, 'store']);
    Route::get('/bahans/{id}', [BahanController::class, 'show']);
    Route::put('/bahans/{id}', [BahanController::class, 'update']);
    Route::delete('/bahans/{id}', [BahanController::class, 'destroy']);

    Route::get('/produks', [ProdukController::class, 'index']);
    Route::post('/produks', [ProdukController::class, 'store']);
    Route::get('/produks/{id}', [ProdukController::class, 'show']);
    Route::put('/produks/{id}', [ProdukController::class, 'update']);
    Route::delete('/produks/{id}', [ProdukController::class, 'destroy']);

    Route::get('/pelanggans', [PelanggansController::class, 'index']);
    Route::post('/pelanggans', [PelanggansController::class, 'store']);
    Route::get('/pelanggans/{id}', [PelanggansController::class, 'show']);
    Route::put('/pelanggans/{id}', [PelanggansController::class, 'update']);
    Route::delete('/pelanggans/{id}', [PelanggansController::class, 'destroy']);

    Route::get('/sizes', [SizeController::class, 'index']);
    Route::post('/sizes', [SizeController::class, 'store']);
    Route::get('/sizes/{id}', [SizeController::class, 'show']);
    Route::put('/sizes/{id}', [SizeController::class, 'update']);
    Route::delete('/sizes/{id}', [SizeController::class, 'destroy']);

    Route::get('/stoks', [StokController::class, 'index']);
    Route::post('/stoks', [StokController::class, 'update']);

    Route::get('/barang-keluar', [BarangKeluarController::class, 'index']);
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store']);

    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::post('/karyawan', [KaryawanController::class, 'store']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
}


);
