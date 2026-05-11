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
        $assets = Asset::with('category')->get();
        $categories = \App\Models\Category::all();

        return view('assets.loan', compact('peminjaman', 'assets', 'categories'));
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

        // MAX 5
        if (count($request->asset_id) > 5) {
            return back()->with('error', 'Maksimal 5 asset!');
        }

        //  CEK KETERSEDIAAN
        foreach ($request->asset_id as $id) {
            $asset = Asset::find($id);

            if (!$asset || $asset->status !== 'tersedia') {
                return back()->with('error', 'Ada asset tidak tersedia!');
            }
        }

        // ✅ SIMPAN LOAN
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'borrow_date' => $request->borrow_date,
            'return_date' => $request->return_date,
            'status' => 'dipinjam',
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