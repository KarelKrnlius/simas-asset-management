<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanHistoryController extends Controller
{
    /**
     * Display a listing of all loans (active and returned)
     */
    public function index()
    {
        // Get search parameter
        $search = request('search');
        
        // Build query with relationships
        $query = Loan::with(['user', 'assets.category']);
        
        // Apply search filter (case-insensitive) - search in user name, email, loan_code
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
            })->orWhere('id', 'LIKE', '%' . str_replace('PIN-', '', $search) . '%');
        }
        
        // Order by latest
        $query->orderBy('created_at', 'desc');
        
        // Paginate
        $loans = $query->paginate(15);
        
        // Append search parameter to pagination links
        $loans->appends(request()->only(['search']));
        
        return view('loan-history.index', compact('loans'));
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
        
        // Tambahkan status berdasarkan condition dari pivot
        foreach ($loan->assets as $asset) {
            if ($loan->status === 'dikembalikan') {
                $pivotCondition = $asset->pivot->condition ?? null;
                if ($pivotCondition === 'hilang') {
                    $asset->return_status = 'tidak ditemukan';
                    $asset->display_condition = 'hilang';
                } else {
                    $asset->return_status = 'dikembalikan';
                    $asset->display_condition = $pivotCondition ?? 'baik';
                }
            } else {
                $asset->return_status = 'dipinjam';
                $asset->display_condition = '-';
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $loan
        ]);
    }
}
