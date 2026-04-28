<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

        static::creating(function ($loan) {
            if (Auth::check()) {
                $loan->created_by = Auth::id();
            }
        });

        static::updating(function ($loan) {
            if (Auth::check()) {
                $loan->updated_by = Auth::id();
            }
        });

        static::deleting(function ($loan) {
            if (Auth::check()) {
                $loan->deleted_by = Auth::id();
                $loan->save();
            }
        });
    }

    // =====================
    // RELATIONS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Use loan_details table for asset relationship
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'loan_details', 'loan_id', 'asset_id')
                    ->withPivot('quantity');
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