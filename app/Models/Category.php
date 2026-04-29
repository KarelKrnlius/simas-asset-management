<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the assets for the category.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
