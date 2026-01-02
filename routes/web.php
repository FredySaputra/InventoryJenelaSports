<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});
Route::view('/login', 'auth.login')->name('login');
Route::view('/dashboard', 'admin.dashboard');
Route::view('/pelanggans', 'admin.pelanggan');
Route::view('/stok-barang', 'admin.stok');
Route::view('/barang-keluar', 'admin.barang_keluar');
Route::view('/karyawan', 'admin.karyawan');
Route::view('/kategori-bahan', 'admin.kategori_bahan');
