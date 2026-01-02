<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PelanggansController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BahanController;

Route::post('/users/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
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

    Route::get('/pelanggans', [PelanggansController::class, 'index']);
    Route::post('/pelanggans', [PelanggansController::class, 'store']);
    Route::get('/pelanggans/{id}', [PelanggansController::class, 'show']);
    Route::put('/pelanggans/{id}', [PelanggansController::class, 'update']);
    Route::delete('/pelanggans/{id}', [PelanggansController::class, 'destroy']);
});
