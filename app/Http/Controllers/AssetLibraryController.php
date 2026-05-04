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

    public function scan()
    {
        return view('asset-library.scan');
    }

    public function showAsset($id)
    {
        $asset = Asset::with(['category', 'loans' => function($query) {
            $query->with('user')->latest();
        }])->findOrFail($id);

        return view('asset-library.show', compact('asset'));
    }

    public function searchByQrCode(Request $request)
    {
        $qrCode = $request->input('qr_code');
        
        if (!$qrCode) {
            return redirect()->route('asset-library.index')->with('error', 'Kode asset harus diisi');
        }
        
        // Cari asset berdasarkan QR code atau kode asset
        $asset = Asset::where('code', $qrCode)
                      ->orWhere('qr_code', $qrCode)
                      ->first();

        if ($asset) {
            return redirect()->route('asset-library.show', $asset->id);
        }

        return redirect()->route('asset-library.index')->with('error', 'Asset tidak ditemukan dengan kode: ' . $qrCode);
    }

    /**
     * QR Code Generator Page
     */
    public function qrGenerator()
    {
        // Get all assets with their categories
        $assets = \App\Models\Asset::with('category')->get();
        
        return view('asset-library.qr-generator', compact('assets'));
    }
}
