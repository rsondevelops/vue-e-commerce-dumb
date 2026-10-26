<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'title' => 'Kekayaan Nusantara di Ujung Jari Anda',
            'subtitle' => 'Belanja produk asli daerah, dari kopi hingga kerajinan tangan',
            'image' => 'banners/banner-1.jpg',
            'link_url' => '/produk',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Diskon Spesial Kopi Nusantara',
            'subtitle' => 'Nikmati potongan harga untuk semua varian kopi pilihan',
            'image' => 'banners/banner-2.jpg',
            'link_url' => '/produk?kategori=kopi-nusantara',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Banner::create([
            'title' => 'Kerajinan Tangan Asli Pengrajin Lokal',
            'subtitle' => 'Setiap produk dibuat dengan tangan oleh pengrajin terbaik',
            'image' => 'banners/banner-3.jpg',
            'link_url' => '/produk?kategori=kerajinan-tangan',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}