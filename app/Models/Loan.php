<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'borrow_date',
        'return_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-fill created_by saat create
        static::creating(function ($loan) {
            if (auth()->check()) {
                $loan->created_by = auth()->id();
            }
        });

        // Auto-fill updated_by saat update
        static::updating(function ($loan) {
            if (auth()->check()) {
                $loan->updated_by = auth()->id();
            }
        });

        // Auto-fill deleted_by saat soft delete
        static::deleting(function ($loan) {
            if (auth()->check()) {
                $loan->deleted_by = auth()->id();
                $loan->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
