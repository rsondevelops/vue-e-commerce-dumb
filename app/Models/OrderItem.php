<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'qty',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'integer',
            'qty' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    /**
     * Relasi: item ini milik satu order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: item ini merujuk ke satu produk asli (bisa null jika produk sudah dihapus permanen).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}