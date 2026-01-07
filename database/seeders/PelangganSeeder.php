<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'     => 'PLG-001',
                'nama'   => 'Toko Olahraga Jaya',
                'alamat' => 'Jl. Ciledug Raya No. 88, Tangerang Selatan',
                'kontak' => '081234567890',
            ],
            [
                'id'     => 'PLG-002',
                'nama'   => 'Dojo Karate Sejahtera',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'kontak' => '081987654321',
            ],
            [
                'id'     => 'PLG-003',
                'nama'   => 'SMK Negeri 1 Jakarta',
                'alamat' => 'Jl. Budi Utomo No. 7, Jakarta Pusat',
                'kontak' => '0213456789',
            ],
            [
                'id'     => 'PLG-UMUM',
                'nama'   => 'Pelanggan Umum (Cash)',
                'alamat' => '-',
                'kontak' => '-',
            ],
        ];

        foreach ($data as $item) {
            Pelanggan::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }
    }
}
