<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'name',
        'status',
    ];

    public function peminjaman()
    {
        return $this->belongsToMany(Peminjaman::class, 'asset_peminjaman');
    }
}