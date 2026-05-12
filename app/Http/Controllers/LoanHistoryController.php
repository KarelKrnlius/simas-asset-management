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
        // Get search and sort parameters
        $search = request('search');
        $sort = request('sort', 'terbaru'); // Default: terbaru
        
        // Build query with relationships
        $query = Loan::with(['user', 'assets.category']);
        
        // Filter by logged in user (only show their own loans)
        $query->where('user_id', auth()->id());
        
        // Apply search filter (case-insensitive) - search in user name, email, loan id, dates, status
        if ($search) {
            $query->where(function($q) use ($search) {
                // Search in user name and email
                $q->whereHas('user', function($subQ) use ($search) {
                    $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                         ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                // Search in loan id (PIN-XXX format)
                ->orWhere('id', 'LIKE', '%' . str_replace('PIN-', '', $search) . '%')
                // Search in borrow_date
                ->orWhere('borrow_date', 'LIKE', '%' . $search . '%')
                // Search in return_date
                ->orWhere('return_date', 'LIKE', '%' . $search . '%')
                // Search in status
                ->orWhere('status', 'LIKE', '%' . $search . '%');
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
        
        return view('loan-history.index', compact('loans', 'sort'));
    }

    /**
     * Get loan details with assets
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'assets.category'])
            ->where('user_id', auth()->id())
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
