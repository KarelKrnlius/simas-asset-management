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
        // pakai Loan, tapi tetap kirim ke variable $peminjaman biar view aman
        $peminjaman = Loan::with(['assets', 'user'])->latest()->get();
        $assets = Asset::all();

        return view('asset.loan', compact('peminjaman', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|array',
            'asset_id.*' => 'exists:assets,id',
            'borrow_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        if (count($request->asset_id) > 5) {
            return back()->with('error', 'Maksimal 5 asset!');
        }

        // cek availability
        foreach ($request->asset_id as $id) {
            $asset = Asset::find($id);

            if (!$asset || $asset->status !== 'tersedia') {
                return back()->with('error', 'Ada asset tidak tersedia!');
            }
        }

        // create loan
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'borrow_date' => $request->borrow_date,
            'return_date' => $request->return_date,
            'status' => 'pending',
        ]);

        // attach assets
        $loan->assets()->attach($request->asset_id);

        // update status asset
        Asset::whereIn('id', $request->asset_id)->update([
            'status' => 'dipinjam'
        ]);

        return back()->with('success', 'Peminjaman berhasil!');
    }

    public function destroy(Loan $loan)
    {
        // ambil asset id
        $assetIds = $loan->assets()->pluck('assets.id');

        // balikin status asset
        Asset::whereIn('id', $assetIds)->update([
            'status' => 'tersedia'
        ]);

        // detach & delete
        $loan->assets()->detach();
        $loan->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}