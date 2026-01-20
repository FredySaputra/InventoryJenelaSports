<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () { return redirect('/login'); });
Route::view('/login', 'auth.login')->name('login');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/produksi/spk', function () {
    return view('admin.produksi.spk');
})->name('produksi.spk');
Route::get('/produksi/verifikasi', function () {
    return view('admin.produksi.verifikasi');
})->name('produksi.verifikasi');

Route::view('/pelanggans', 'admin.pelanggan');
Route::view('/stok-barang', 'admin.stok_barang');
Route::view('/barang-keluar', 'admin.barang_keluar');
Route::view('/karyawan', 'admin.data_karyawan');
Route::view('/kategori-bahan', 'admin.kategori_bahan');
Route::view('/produk','admin.produk')->name('produk');
Route::view('/size','admin.size')->name('size');
