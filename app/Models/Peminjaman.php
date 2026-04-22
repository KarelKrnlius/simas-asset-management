<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'borrow_date',
        'return_date',
        'status',
    ];

    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // MANY TO MANY assets
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_peminjaman');
    }
}