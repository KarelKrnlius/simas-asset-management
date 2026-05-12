<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $peminjaman = Loan::with(['assets', 'user'])->latest()->get();
        
        // Ambil semua assets dan sort berdasarkan kode asset (angka di kode)
        $assets = Asset::with('category')->get()->sort(function($a, $b) {
            // Extract angka dari kode untuk sorting (misal: BRIN-01-000003 -> 3)
            preg_match('/\d+$/', $a->code, $matchesA);
            preg_match('/\d+$/', $b->code, $matchesB);
            
            $numberA = isset($matchesA[0]) ? (int)$matchesA[0] : 0;
            $numberB = isset($matchesB[0]) ? (int)$matchesB[0] : 0;
            
            return $numberA - $numberB;
        })->values();
            
        $categories = \App\Models\Category::all();
        
        // Ambil jumlah pinjaman aktif user saat ini
        $activeLoansCount = Loan::where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->withCount('assets')
            ->get()
            ->sum('assets_count');

        return view('assets.loan', compact('peminjaman', 'assets', 'categories', 'activeLoansCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|array',
            'asset_id.*' => 'exists:assets,id',
            'borrow_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        // CEK DUPLIKAT
        if (count(array_unique($request->asset_id)) != count($request->asset_id)) {
            return back()->with('error', 'Asset tidak boleh sama!');
        }

        // DYNAMIC BORROWING LIMIT - CEK JUMLAH PINJAMAN AKTIF SAAT INI
        $currentUserId = Auth::id();
        $activeLoansCount = Loan::where('user_id', $currentUserId)
            ->where('status', 'dipinjam')
            ->withCount('assets')
            ->get()
            ->sum('assets_count');
        
        $requestedItems = count($request->asset_id);
        $totalAfterBorrow = $activeLoansCount + $requestedItems;
        
        // CEK BATAS MAKSIMAL 5
        if ($totalAfterBorrow > 5) {
            $availableSlots = 5 - $activeLoansCount;
            if ($availableSlots <= 0) {
                return back()->with('error', 'Anda sudah mencapai batas maksimal 5 peminjaman. Kembalikan minimal 1 barang untuk bisa meminjam lagi.');
            } else {
                return back()->with('error', "Anda sedang meminjam {$activeLoansCount} barang. Maksimal bisa tambah {$availableSlots} barang lagi.");
            }
        }
        
        // VALIDASI STATIS - TIDAK BOLEH LEBIH DARI 5 SEKALIGAN
        if ($requestedItems > 5) {
            return back()->with('error', 'Maksimal 5 asset per peminjaman!');
        }

        //  CEK KETERSEDIAAN
        foreach ($request->asset_id as $id) {
            $asset = Asset::find($id);

            if (!$asset || $asset->status !== 'tersedia') {
                return back()->with('error', 'Ada asset tidak tersedia!');
            }
        }

        // Generate unique loan code
        $lastLoan = Loan::whereNotNull('loan_code')->latest()->first();
        if ($lastLoan && $lastLoan->loan_code) {
            $lastNumber = intval(substr($lastLoan->loan_code, -6));
        } else {
            $lastNumber = 0;
        }
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $loanCode = 'PIN-' . $newNumber;

        // ✅ SIMPAN LOAN
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'asset_id' => $request->asset_id[0], // Ambil asset pertama sebagai primary asset
            'borrow_date' => $request->borrow_date,
            'return_date' => $request->return_date,
            'status' => 'dipinjam',
            'loan_code' => $loanCode, // Tambah kode unik
        ]);

// attach assets with required quantity pivot value
        $attachData = collect($request->asset_id)
            ->unique()
            ->mapWithKeys(fn ($assetId) => [$assetId => ['quantity' => 1]])
            ->all();

        $loan->assets()->attach($attachData);

        // update status asset and decrease stock
        foreach (array_keys($attachData) as $assetId) {
            $asset = Asset::find($assetId);
            if ($asset) {
                $newStock = max(0, $asset->stock - 1);
                $asset->update([
                    'status' => 'dipinjam',
                    'stock' => $newStock
                ]);
            }
        }

        return back()->with('success', 'Peminjaman berhasil!');
    }

    public function destroy(Loan $loan)
    {
        // ambil asset id
        $assetIds =$loan->assets()->pluck('assets.id');

        // balikin status asset and increase stock
        foreach ($assetIds as $assetId) {
            $asset = Asset::find($assetId);
            if ($asset) {
                $newStock = $asset->stock + 1;
                $asset->update([
                    'status' => 'tersedia',
                    'stock' => $newStock
                ]);
            }
        }

        // detach & delete
        $loan->assets()->detach();
        $loan->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}