<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kopi Nusantara',
            'Elektronik',
            'Gadget & Smartphone',
            'Aksesoris Gadget',
            'Peralatan Kantor',
            'Peralatan Rumah',
            'Mainan & Hobi',
            'Perlengkapan Sekolah',
            'Alat Pertukangan',
            'Tanaman & Gardening',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'is_active' => true,
            ]);
        }
    }
}