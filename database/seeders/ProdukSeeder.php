<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?? User::create([
            'username' => 'admin',
            'nama' => 'Admin',
            'role' => 'Admin',
            'password' => Hash::make('password')
        ]);

        $bhnTp = Bahan::updateOrCreate(['id' => 'BHN-TP'], [
            'nama' => 'TP',
            'deskripsi' => 'Bahan TP Standar',
            'idKategori' => 'KAT-01'
        ]);

        $bhnDrill = Bahan::updateOrCreate(['id' => 'BHN-DRILL'], [
            'nama' => 'Drill',
            'deskripsi' => 'Kain Drill',
            'idKategori' => 'KAT-01'
        ]);

        $bhnTebal = Bahan::updateOrCreate(['id' => 'BHN-TEBAL'], [
            'nama' => 'Tebal',
            'deskripsi' => 'Kain Tebal Premium',
            'idKategori' => 'KAT-01'
        ]);
        $bhnBiasa = Bahan::updateOrCreate(['id' => 'BHN-BIASA'], [
            'nama' => 'Biasa',
            'deskripsi' => 'Sabuk Standar',
            'idKategori' => 'KAT-02'
        ]);

        $bhnBordir = Bahan::updateOrCreate(['id' => 'BHN-BORDIR'], [
            'nama' => 'Tebal Bordir',
            'deskripsi' => 'Sabuk Tebal dengan Bordir',
            'idKategori' => 'KAT-02'
        ]);

        $bhnSilat = Bahan::updateOrCreate(['id' => 'BHN-SILAT'], [
            'nama' => 'Silat',
            'deskripsi' => 'Khusus Silat',
            'idKategori' => 'KAT-02'
        ]);

        $produks = [
            [
                'id' => 'KRT-TP', 'nama' => 'Baju Karate', 'warna' => null,
                'idBahan' => 'BHN-TP', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'KRT-DR', 'nama' => 'Baju Karate', 'warna' => null,
                'idBahan' => 'BHN-DRILL', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'KRT-TB', 'nama' => 'Baju Karate', 'warna' => null,
                'idBahan' => 'BHN-TEBAL', 'idKategori' => 'KAT-01'
            ],

            [
                'id' => 'TKD-TP', 'nama' => 'Baju Taekwondo', 'warna' => null,
                'idBahan' => 'BHN-TP', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'TKD-DR', 'nama' => 'Baju Taekwondo', 'warna' => null,
                'idBahan' => 'BHN-DRILL', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'TKD-TB', 'nama' => 'Baju Taekwondo', 'warna' => null,
                'idBahan' => 'BHN-TEBAL', 'idKategori' => 'KAT-01'
            ],

            [
                'id' => 'TKD-MH-TP', 'nama' => 'Baju Taekwondo', 'warna' => 'Merah Hitam',
                'idBahan' => 'BHN-TP', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'TKD-MH-DR', 'nama' => 'Baju Taekwondo', 'warna' => 'Merah Hitam',
                'idBahan' => 'BHN-DRILL', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'TKD-MH-TB', 'nama' => 'Baju Taekwondo', 'warna' => 'Merah Hitam',
                'idBahan' => 'BHN-TEBAL', 'idKategori' => 'KAT-01'
            ],

            // 4. Baju Silat (TP, Drill, Tebal)
            [
                'id' => 'SLT-TP', 'nama' => 'Baju Silat', 'warna' => null,
                'idBahan' => 'BHN-TP', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'SLT-DR', 'nama' => 'Baju Silat', 'warna' => null,
                'idBahan' => 'BHN-DRILL', 'idKategori' => 'KAT-01'
            ],
            [
                'id' => 'SLT-TB', 'nama' => 'Baju Silat', 'warna' => null,
                'idBahan' => 'BHN-TEBAL', 'idKategori' => 'KAT-01'
            ],

            // ---------------------------------------
            // KATEGORI: SABUK (KAT-02)
            // ---------------------------------------

            // Sabuk (Biasa, Tebal Bordir, Silat)
            [
                'id' => 'SBK-BS', 'nama' => 'Sabuk', 'warna' => null,
                'idBahan' => 'BHN-BIASA', 'idKategori' => 'KAT-02'
            ],
            [
                'id' => 'SBK-TB', 'nama' => 'Sabuk', 'warna' => null,
                'idBahan' => 'BHN-BORDIR', 'idKategori' => 'KAT-02'
            ],
            [
                'id' => 'SBK-SL', 'nama' => 'Sabuk', 'warna' => null,
                'idBahan' => 'BHN-SILAT', 'idKategori' => 'KAT-02'
            ],
        ];

        foreach ($produks as $p) {
            Produk::updateOrCreate(
                ['id' => $p['id']],
                [
                    'nama'       => $p['nama'],
                    'warna'      => $p['warna'],
                    'idBahan'    => $p['idBahan'],
                    'idKategori' => $p['idKategori'],
                    'idUser'     => $admin->id
                ]
            );
        }
    }
}
