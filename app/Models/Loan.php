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
        'asset_id',
        'borrow_date',
        'return_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'loan_code'
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

    /**
     * Relasi Many-to-Many ke Asset melalui tabel perantara loan_details.
     * Menggunakan withPivot agar bisa mengakses 'quantity' barang yang dipinjam.
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'loan_details', 'loan_id', 'asset_id')
                    ->withPivot('quantity', 'condition');
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

    /**
     * Generate kode peminjaman unik
     * Format: PIN-000001 (flexible digit)
     * ID 1-9: PIN-000001
     * ID 10-99: PIN-000010  
     * ID 100-999: PIN-000100
     * ID 1000-9999: PIN-001000
     * ID 10000-99999: PIN-010000
     * ID 100000-999999: PIN-100000
     * ID 1000000-9999999: PIN-1000000
     * ... dst otomatis menyesuaikan
     */
    public function getLoanCodeAttribute()
    {
        // Hitung jumlah digit ID
        $idLength = strlen((string)$this->id);
        
        // Minimal 6 digit, maksimal sesuai kebutuhan
        $totalDigits = max(6, $idLength);
        
        return 'PIN-' . str_pad($this->id, $totalDigits, '0', STR_PAD_LEFT);
    }
}