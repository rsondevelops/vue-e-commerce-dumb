<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];
    
    protected $appends = ['formatted_price'];
    
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi: produk ini milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: produk ini pernah muncul di banyak order_items (histori transaksi).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope: hanya produk aktif (dipakai katalog publik, Chapter 13).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: hanya produk dengan stok tersedia.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Accessor: harga dalam format "Rp 45.000" siap pakai di Vue.
     */
    protected function formattedPrice(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => 'Rp ' . number_format($this->price, 0, ',', '.'),
        );
    }
}