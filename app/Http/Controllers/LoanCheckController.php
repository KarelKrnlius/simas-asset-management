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
        
        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            
            $query->where(function($q) use ($search, $searchLower) {
                // 1. PRIORITAS UTAMA: Search in loan_code
                $q->where('loan_code', 'LIKE', '%' . $search . '%');
                
                // 2. Search in user name and email
                $q->orWhereHas('user', function($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', '%' . $search . '%')
                         ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
                
                // 3. Search in dates - direct format (2026-05-12)
                $q->orWhere(function($dateQ) use ($search) {
                    $dateQ->where('borrow_date', 'LIKE', '%' . $search . '%')
                          ->orWhere('return_date', 'LIKE', '%' . $search . '%');
                });
                
                // 4. Parse Indonesian date format (12 mei, 12 mei 2026)
                $monthsIndo = [
                    'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
                    'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
                    'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
                ];
                
                foreach ($monthsIndo as $monthName => $monthNum) {
                    if (strpos($searchLower, $monthName) !== false) {
                        // Try to extract day and year
                        if (preg_match('/(\d{1,2})\s*' . $monthName . '\s*(\d{4})?/i', $searchLower, $matches)) {
                            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                            $year = isset($matches[2]) ? $matches[2] : date('Y');
                            $datePattern = $year . '-' . $monthNum . '-' . $day;
                            
                            $q->orWhere(function($dateQ) use ($datePattern) {
                                $dateQ->where('borrow_date', $datePattern)
                                      ->orWhere('return_date', $datePattern);
                            });
                        } else {
                            // Just month name - search all dates in that month
                            $q->orWhere(function($monthQ) use ($monthNum) {
                                $monthQ->whereRaw("EXTRACT(MONTH FROM borrow_date) = ?", [$monthNum])
                                       ->orWhereRaw("EXTRACT(MONTH FROM return_date) = ?", [$monthNum]);
                            });
                        }
                        break;
                    }
                }
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

        // Tambahkan kondisi terakhir dari master asset
        foreach ($loan->assets as $asset) {
            $asset->display_condition = $asset->condition ?? 'baik';
        }
        
        return response()->json([
            'success' => true,
            'data' => $loan
        ]);
    }

    /**
     * Bulk delete loans
     */
    public function bulkDelete(Request $request)
    {
        $loanIds = $request->input('loan_ids', []);
        
        // Ensure loan_ids is an array
        if (!is_array($loanIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Format data tidak valid'
            ], 400);
        }
        
        if (empty($loanIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada peminjaman yang dipilih'
            ], 400);
        }
        
        try {
            // Get all loans to be deleted
            $loans = Loan::whereIn('id', $loanIds)->with('assets')->get();
            $deletedCount = $loans->count();
            
            foreach ($loans as $loan) {
                // Return assets to available status and increment stock
                foreach ($loan->assets as $asset) {
                    $quantity = $asset->pivot->quantity;
                    
                    // Increment stock
                    $newStock = $asset->stock + $quantity;
                    
                    // Update asset status to 'tersedia' if stock is available
                    $asset->update([
                        'stock' => $newStock,
                        'status' => 'tersedia'
                    ]);
                }
                
                // Delete the loan (soft delete)
                $loan->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' peminjaman berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
