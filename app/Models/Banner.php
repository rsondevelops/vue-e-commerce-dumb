<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope: hanya banner aktif, diurutkan sesuai sort_order (dipakai homepage, Chapter 12).
     */
    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}