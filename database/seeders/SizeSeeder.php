<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Size;
use App\Models\Kategori;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Kategori ada
        $this->ensureCategoriesExist();

        // Data Size (Sama seperti sebelumnya)
        $sizes = [
            // Baju Beladiri (KAT-01)
            ['id' => 'BJU-S',  'tipe' => 'S',  'panjang' => 60, 'lebar' => 45, 'idKategori' => 'KAT-01'],
            ['id' => 'BJU-M',  'tipe' => 'M',  'panjang' => 65, 'lebar' => 50, 'idKategori' => 'KAT-01'],
            ['id' => 'BJU-L',  'tipe' => 'L',  'panjang' => 70, 'lebar' => 55, 'idKategori' => 'KAT-01'],
            ['id' => 'BJU-XL', 'tipe' => 'XL', 'panjang' => 75, 'lebar' => 60, 'idKategori' => 'KAT-01'],

            // Sabuk (KAT-02)
            ['id' => 'SBK-KCL', 'tipe' => 'Kecil',   'panjang' => 240, 'lebar' => 4, 'idKategori' => 'KAT-02'],
            ['id' => 'SBK-STD', 'tipe' => 'Standar', 'panjang' => 280, 'lebar' => 4, 'idKategori' => 'KAT-02'],

            // Baju Renang (KAT-03)
            ['id' => 'RNG-L',  'tipe' => 'L',  'panjang' => 62, 'lebar' => 40, 'idKategori' => 'KAT-03'],
            ['id' => 'RNG-XL', 'tipe' => 'XL', 'panjang' => 66, 'lebar' => 44, 'idKategori' => 'KAT-03'],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(['id' => $size['id']], $size);
        }
    }

    private function ensureCategoriesExist()
    {
        // HAPUS 'prefix_size' DARI SINI
        $kategoris = [
            ['id' => 'KAT-01', 'nama' => 'Baju Beladiri'],
            ['id' => 'KAT-02', 'nama' => 'Sabuk'],
            ['id' => 'KAT-03', 'nama' => 'Baju Renang'],
        ];

        foreach ($kategoris as $k) {
            if (!Kategori::find($k['id'])) {
                Kategori::create($k);
            }
        }
    }
}
