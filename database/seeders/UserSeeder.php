<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin — dipakai untuk login ke panel admin (Chapter 6)
        User::create([
            'name' => 'Admin E-Commerce RSON',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081200000001',
            'address' => 'Kantor Pusat E-Commerce RSON, Jakarta',
        ]);

        // Beberapa akun customer percobaan
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '081200000002',
            'address' => 'Jl. Merdeka No. 10, Bandung',
        ]);

        User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '081200000003',
            'address' => 'Jl. Sudirman No. 25, Surabaya',
        ]);
    }
}