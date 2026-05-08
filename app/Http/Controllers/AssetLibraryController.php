<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetLibraryController extends Controller
{
    public function index()
    {
        return redirect()->route('asset-library.scan');
    }

    public function qrGenerator()
    {
        $assets = Asset::all();
        return view('asset-library.qr-generator', compact('assets'));
    }

    public function scan()
    {
        return view('asset-library.scan');
    }

 public function showAsset($code)
{
    $asset = Asset::where('code', $code)->firstOrFail();
    
    // Load relasi loans dengan user untuk riwayat penggunaan
    $asset->load(['loans.user']);
    
    return view('asset-library.show', compact('asset'));
}

public function searchByQrCode(Request $request)
{
    $qrCode = $request->input('qr_code');

    if (!$qrCode) {
        return redirect()->route('asset-library.index')->with('error', 'Kode asset harus diisi');
    }

    // Cari asset berdasarkan kode asset
    $asset = Asset::where('code', $qrCode)->first();

    if ($asset) {
        return redirect()->route('asset-library.show', $asset->code);
    }

    return redirect()->route('asset-library.index')->with('error', 'Asset tidak ditemukan dengan kode: ' . $qrCode);
}

public function store(Request $request)
{
    dd($request->all()); 
}
}