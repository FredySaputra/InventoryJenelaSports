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
        User::create([
            'id' => 'ADM1',
            'nama' => 'Admin 1',
            'noTelp' => '085248239843',
            'username' => 'admin',
            'password' => Hash::make('rahasia'),
            'role' => 'Admin'
        ]);
    }
}
