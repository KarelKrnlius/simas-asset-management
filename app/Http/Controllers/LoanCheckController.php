<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanCheckController extends Controller
{
    /**
     * Display a listing of loans for checking
     */
    public function index()
    {
        // Get search parameter
        $search = request('search');
        
        // Build query with relationships
        $query = Loan::with(['user', 'assets.category']);
        
        // HANYA TAMPILKAN PEMINJAMAN YANG BELUM DIKEMBALIKAN (status = dipinjam)
        $query->where('status', 'dipinjam');
        
        // Apply search filter (case-insensitive) - search in user name, email
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }
        
        // Order by latest
        $query->orderBy('created_at', 'desc');
        
        // Paginate
        $loans = $query->paginate(15);
        
        // Append search parameter to pagination links
        $loans->appends(request()->only(['search']));
        
        return view('loan-check.index', compact('loans'));
    }

    /**
     * Get loan details with assets
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'assets.category'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $loan
        ]);
    }
}
