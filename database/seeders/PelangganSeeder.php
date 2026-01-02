<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            [
                'id' => 'PLG-001',
                'nama' => 'PT. Niaga Bersama',
                'alamat' => 'Jl. Cidodol Raya, RT.005/RW.008, No.84, Kebayoran Lama, Jakarta Selatan',
                'kontak' => '085219248293'
            ],
            [
                'id' => 'PLG-002',
                'nama' => 'PT. Jaya Abadi',
                'alamat' => 'Jl. Ciledug Raya, RT.002/RW.004, No.80, Ciledug, Tangerang Selatan',
                'kontak' => '085242332819'
            ]
        ];

        foreach ($collections as $collection) {
            Pelanggan::create($collection);
        }

    }
}
