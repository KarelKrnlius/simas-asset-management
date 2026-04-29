<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
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

    /**
     * Get the category that owns the asset.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi many-to-many ke peminjaman melalui tabel loan_details.
     */
    public function loans()
    {
        return $this->belongsToMany(Loan::class, 'loan_details')
                    ->withPivot('quantity');
    }
}

