<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'description',
        'stock',
        'condition',
        'status',
    ];

    // =====================
    // RELASI KE CATEGORY
    // =====================
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // =====================
    // RELASI KE PEMINJAMAN (kalau dipakai)
    // =====================
    public function peminjaman()
    {
        return $this->belongsToMany(Peminjaman::class, 'asset_peminjaman');
    }
}