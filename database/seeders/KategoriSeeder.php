<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'KAT-01',
                'nama' => 'Baju Beladiri'
            ],
            [
                'id' => 'KAT-02',
                'nama' => 'Sabuk'
            ]
        ];

        Kategori::insert($data);
    }
}
