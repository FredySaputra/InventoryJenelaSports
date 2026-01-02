<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'BJU-01',
                'nama' => 'TP',
                'deskripsi' => 'Bahan standar/tipis (Tetoron Rayon/Polyester)',
                'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'BJU-02',
                'nama' => 'Drill',
                'deskripsi' => 'Bahan kain drill (Japan/American Drill)',
                'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'BJU-03',
                'nama' => 'Tebal',
                'deskripsi' => 'Bahan canvas atau kain keras tebal',
                'idKategori' => 'KAT-01'
            ],

            [
                'id' => 'SBK-01',
                'nama' => 'Biasa',
                'deskripsi' => 'Sabuk kain standar pemula',
                'idKategori' => 'KAT-02'
            ],
            [
                'id' => 'SBK-02',
                'nama' => 'Tebal Bordir',
                'deskripsi' => 'Sabuk tebal dengan lapisan keras',
                'idKategori' => 'KAT-02'
            ],
            [
                'id' => 'SBK-03',
                'nama' => 'Silat',
                'deskripsi' => 'Bahan mengkilap atau kain khusus sabuk silat',
                'idKategori' => 'KAT-02'
            ],
        ];

        Bahan::insert($data);
    }
}
