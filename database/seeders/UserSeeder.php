<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'id' => 'ADM1',
                'nama' => 'Admin 1',
                'noTelp' => '085248239843',
                'password' => Hash::make('rahasia'),
                'role' => 'Admin'
            ]
        );

        for ($i = 1; $i <= 10; $i++) {
            $nomorUrut = str_pad($i, 3, '0', STR_PAD_LEFT);

            User::updateOrCreate(
                ['username' => 'karyawan' . $i],
                [
                    'id' => 'KRY-' . $nomorUrut,
                    'nama' => fake('id_ID')->name(),
                    'noTelp' => '08' . mt_rand(1111111111, 9999999999),
                    'password' => Hash::make('rahasia'),
                    'role' => 'Karyawan'
                ]
            );
        }
    }
}
