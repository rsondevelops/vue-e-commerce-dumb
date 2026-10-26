<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 15 produk dengan stok normal
        Product::factory()->count(50)->create();

        // 5 produk dengan stok menipis (untuk uji tampilan "stok rendah" di Chapter 19)
        Product::factory()->count(5)->create([
            'stock' => fake()->numberBetween(1, 4),
        ]);

        // 3 produk stok habis (untuk uji tombol "Habis" di katalog, Chapter 13-14)
        Product::factory()->count(10)->create([
            'stock' => 0,
        ]);
    }
}