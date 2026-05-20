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
        
        // Menu ini hanya untuk user biasa (non-admin).
        // Admin punya menu tersendiri: Pengecek Peminjaman.
        // Selalu filter berdasarkan user yang sedang login, tanpa pengecualian.
        $query->where('user_id', auth()->id());
        
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
                
                // 5. Search in status (Indonesian)
                if (strpos($searchLower, 'aktif') !== false || strpos($searchLower, 'dipinjam') !== false) {
                    $q->orWhere('status', 'dipinjam');
                }
                if (strpos($searchLower, 'dikembalikan') !== false || strpos($searchLower, 'kembali') !== false) {
                    $q->orWhere('status', 'dikembalikan');
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
        
        return view('loan-history.index', compact('loans', 'sort'));
    }

    /**
     * Get loan details with assets
     */
    public function show($id)
    {
        $query = Loan::with(['user', 'assets.category']);

        // Admin bisa lihat semua, user biasa hanya miliknya
        if (auth()->user()->role_id != 1) {
            $query->where('user_id', auth()->id());
        }

        $loan = $query->findOrFail($id);
        
        // Tambahkan loan_code ke response
        $loan->loan_code = $loan->loan_code;
        
        // Tentukan status per-asset berdasarkan pivot condition (bukan status loan)
        foreach ($loan->assets as $asset) {
            $pivotCondition = $asset->pivot->condition ?? null;

            if ($pivotCondition !== null) {
                // Asset ini sudah dikembalikan — tampilkan kondisi saat dikembalikan
                if ($pivotCondition === 'hilang') {
                    $asset->return_status    = 'tidak ditemukan';
                    $asset->display_condition = 'hilang';
                } else {
                    $asset->return_status    = 'dikembalikan';
                    $asset->display_condition = $pivotCondition;
                }
            } else {
                // Asset ini belum dikembalikan — tampilkan kondisi terakhir dari master asset
                $asset->return_status    = 'dipinjam';
                $asset->display_condition = $asset->condition ?? 'baik';
            }
        }
        
        return response()->json([
            'success' => true,
            'data'    => $loan
        ]);
    }
}
