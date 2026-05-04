<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role is admin by name.
     */
    public function isAdmin(): bool
    {
        return strtolower($this->name) === 'admin';
    }

    /**
     * Get role by name (static helper).
     */
    public static function getByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }
}
