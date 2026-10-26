<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Daftar nama produk realistis per tema, supaya hasil seed
     * terasa "asli" — bukan nama acak seperti "Product Lorem 42".
     */
    protected array $productNames = [
        // Kopi & Minuman
        'Kopi Gayo 200gr',
        'Kopi Toraja Arabica 250gr',
        'Kopi Kintamani 200gr',
        'Kopi Flores Bajawa 200gr',
        'Kopi Robusta Temanggung 250gr',
        'Teh Melati Premium 100gr',
        'Teh Hijau Jepang 100gr',
        'Cokelat Bubuk Premium 250gr',
        'Matcha Powder 100gr',
        'Susu Almond Original 1L',

        // Makanan & Camilan
        'Keripik Tempe Malang',
        'Dodol Garut Original',
        'Bakpia Pathok Isi Kacang Hijau',
        'Keripik Pisang Cokelat',
        'Basreng Pedas Daun Jeruk',
        'Kacang Bawang Premium',
        'Kerupuk Seblak Pedas',
        'Brownies Cokelat Premium',
        'Cookies Choco Chip 250gr',
        'Granola Madu 300gr',

        // Rempah & Bumbu
        'Bubuk Kunyit Asli',
        'Kayu Manis Kering',
        'Cengkeh Pilihan 100gr',
        'Lada Hitam Premium 100gr',
        'Lada Putih Premium 100gr',
        'Kapulaga Hijau 100gr',
        'Bubuk Cabai Pedas 100gr',
        'Ketumbar Bubuk 100gr',
        'Bumbu Rendang Premium',
        'Bumbu Kari Nusantara',

        // Fashion
        'Batik Tulis Lasem',
        'Batik Cap Pekalongan',
        'Kain Tenun Sumba',
        'Kemeja Linen Pria',
        'Kaos Oversize Basic',
        'Celana Chino Pria',
        'Hoodie Basic Premium',
        'Dress Casual Wanita',
        'Blouse Linen Wanita',
        'Outer Rajut Wanita',

        // Tas & Aksesoris
        'Anyaman Rotan Bali',
        'Tas Selempang Kulit Sintetis',
        'Totebag Kanvas Premium',
        'Dompet Kulit Minimalis',
        'Ransel Laptop 15 Inch',
        'Jam Tangan Casual',
        'Gelang Stainless Steel',
        'Kalung Minimalis Wanita',
        'Topi Baseball Basic',
        'Ikat Pinggang Kulit',

        // Kerajinan & Dekorasi
        'Ukiran Kayu Jepara',
        'Gerabah Kasongan',
        'Lilin Aromaterapi Lavender',
        'Vas Bunga Keramik',
        'Miniatur Rumah Adat',
        'Dekorasi Dinding Minimalis',
        'Lampu Meja Vintage',
        'Rak Kayu Minimalis',
        'Bingkai Foto Kayu',
        'Pot Tanaman Keramik',

        // Elektronik & Gadget
        'Wireless Earphone Bluetooth',
        'Keyboard Mechanical RGB',
        'Mouse Wireless Silent',
        'USB Hub 4 Port',
        'Kabel USB-C Fast Charging',
        'Charger GaN 65W',
        'Power Bank 10000mAh',
        'Webcam Full HD',
        'Speaker Bluetooth Portable',
        'Stand Laptop Aluminium',

        // Rumah Tangga
        'Set Pisau Dapur Stainless',
        'Talenan Kayu Premium',
        'Botol Minum Stainless 750ml',
        'Kotak Makan Bento',
        'Rak Serbaguna 3 Tingkat',
        'Tempat Sampah Minimalis',
        'Set Alat Makan Stainless',
        'Wadah Penyimpanan Kaca',
        'Keset Kaki Microfiber',
        'Sapu Lantai Premium',

        // Skincare & Perawatan
        'Facial Wash Gentle 100ml',
        'Moisturizer Hydrating 50ml',
        'Sunscreen SPF 50 PA++++',
        'Serum Niacinamide 10%',
        'Body Lotion Moisturizing',
        'Shampoo Herbal 250ml',
        'Hair Conditioner 250ml',
        'Parfum Eau de Parfum 50ml',
        'Hand Cream Moisturizing',
        'Lip Balm Natural',

        // Olahraga & Outdoor
        'Kaos Olahraga Dry Fit',
        'Celana Training Pria',
        'Yoga Mat Premium',
        'Resistance Band Set',
        'Botol Minum Sport 1L',
        'Tas Gym Waterproof',
        'Matras Camping Outdoor',
        'Lampu Senter LED',
        'Jaket Outdoor Waterproof',
        'Topi Outdoor UV Protection',

        // Buku & Alat Tulis
        'Notebook Hardcover A5',
        'Planner Mingguan A5',
        'Pulpen Gel Premium',
        'Pensil Mekanik 0.5mm',
        'Highlighter Pastel Set',
        'Sticky Notes Minimalis',
        'Buku Catatan Kuliah',
        'Sketchbook A4',
        'Map Dokumen A4',
        'Set Penggaris Geometri',
    ];
    public function definition(): array
    {
        $name = $this->faker->randomElement($this->productNames);
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->numberBetween(15, 500) * 1000, // kelipatan ribuan
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => 'poster/' . $this->faker->numberBetween(0, 7) . '.png',
            'is_active' => true,
        ];
    }
}