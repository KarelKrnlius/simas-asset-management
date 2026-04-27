<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetReturnController extends Controller
{
    public function index()
    {
        /**
         * Mengambil user yang memiliki pinjaman aktif.
         * Karena tidak ada LoanDetail, kita memanggil relasi 'assets' langsung dari 'loans'.
         */
        $users = User::whereHas('loans', function($q) {
            $q->where('status', '<>', 'dikembalikan');
        })->with(['loans.assets'])->get();

        return view('pengembalian.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'returns' => 'required_without:loan_id|array|min:1',
            'returns.*.loan_id' => 'required_with:returns|exists:loans,id',
            'returns.*.asset_id' => 'required_with:returns|exists:assets,id',
            'returns.*.condition' => 'required_with:returns|in:baik,rusak,hilang',
            'loan_id' => 'required_without:returns|exists:loans,id',
            'asset_id' => 'required_without:returns|exists:assets,id',
            'condition' => 'required_without:returns|in:baik,rusak,hilang',
        ]);

        $returns = $request->input('returns');
        if (!$returns) {
            $returns = [[
                'loan_id' => $request->loan_id,
                'asset_id' => $request->asset_id,
                'condition' => $request->condition,
            ]];
        }

        try {
            DB::transaction(function () use ($returns) {
                $processedLoanIds = [];

                foreach ($returns as $returnItem) {
                    $loan = Loan::findOrFail($returnItem['loan_id']);
                    $asset = Asset::findOrFail($returnItem['asset_id']);

                    // Ambil quantity yang dipinjam dari pivot
                    $pivot = $loan->assets()->where('asset_id', $returnItem['asset_id'])->first();
                    $qtyDipinjam = $pivot ? $pivot->pivot->quantity : 0;

                    if ($returnItem['condition'] === 'baik') {
                        $asset->increment('stock', $qtyDipinjam);
                    } elseif ($returnItem['condition'] === 'rusak') {
                        $asset->increment('stock', $qtyDipinjam);
                        $asset->update(['condition' => 'rusak']);
                    } elseif ($returnItem['condition'] === 'hilang') {
                        $asset->update(['condition' => 'hilang']);
                    }

                    $loan->assets()->detach($returnItem['asset_id']);
                    $processedLoanIds[] = $loan->id;
                }

                foreach (array_unique($processedLoanIds) as $loanId) {
                    $loan = Loan::findOrFail($loanId);
                    if ($loan->assets()->count() == 0) {
                        $loan->update(['status' => 'dikembalikan']);
                    }
                }
            });

            return response()->json(['message' => 'Barang berhasil dikembalikan!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}