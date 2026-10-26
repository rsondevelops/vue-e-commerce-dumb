<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'status',
        'payment_method',
        'total_amount',
        'notes',
        'cancelled_reason',
    ];

    protected $appends = ['formatted_total'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
        ];
    }

    /**
     * Relasi: order ini milik satu user (customer).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: satu order punya banyak item produk.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessor: total dalam format "Rp 125.000".
     */
    protected function formattedTotal(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => 'Rp ' . number_format($this->total_amount, 0, ',', '.'),
        );
    }

    /**
     * Scope: filter order berdasarkan status tertentu (dipakai admin, Chapter 18).
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}