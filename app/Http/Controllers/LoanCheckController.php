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
        // Get search and sort parameters
        $search = request('search');
        $sort = request('sort', 'terbaru'); // Default: terbaru
        
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
        
        // Apply sorting
        switch ($sort) {
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Paginate
        $loans = $query->paginate(15);
        
        // Append search and sort parameters to pagination links
        $loans->appends(request()->only(['search', 'sort']));
        
        return view('loan-check.index', compact('loans', 'sort'));
    }

    /**
     * Get loan details with assets
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'assets.category'])
            ->findOrFail($id);
        
        // Tambahkan loan_code ke response
        $loan->loan_code = $loan->loan_code;
        
        return response()->json([
            'success' => true,
            'data' => $loan
        ]);
    }
}
