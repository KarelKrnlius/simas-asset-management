<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['assets', 'user'])->latest()->get();
        $assets = Asset::all();

        return view('asset.peminjaman', compact('peminjaman', 'assets'));
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

        // create peminjaman (1 record saja)
        $peminjaman = Peminjaman::create([
            'user_id' => Auth::id(),
            'borrow_date' => $request->borrow_date,
            'return_date' => $request->return_date,
            'status' => 'pending',
        ]);

        // attach assets
        $peminjaman->assets()->attach($request->asset_id);

        // update status asset
        Asset::whereIn('id', $request->asset_id)->update([
            'status' => 'dipinjam'
        ]);

        return back()->with('success', 'Peminjaman berhasil!');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        // balikin asset
        $assetIds = $peminjaman->assets()->pluck('assets.id');

        Asset::whereIn('id', $assetIds)->update([
            'status' => 'tersedia'
        ]);

        $peminjaman->assets()->detach();
        $peminjaman->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}