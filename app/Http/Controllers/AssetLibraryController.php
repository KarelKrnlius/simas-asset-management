<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function publicScan()
    {
        return view('asset-library.public-scan');
    }

    public function showAsset($code)
    {
        $asset = Asset::where('code', $code)->firstOrFail();
        $asset->load(['loans.user']);
        
        return view('asset-library.show', compact('asset'));
    }

public function searchByQrCode(Request $request)
{
    $qrCode = $request->input('qr_code');

    if (!$qrCode) {
        if (Auth::check()) {
            return redirect()->route('asset-library.index')->with('error', 'Kode asset harus diisi');
        }
        return redirect()->route('asset.public', 'notfound');
    }

    // Cari asset berdasarkan kode asset
    $asset = Asset::where('code', $qrCode)->first();

    if ($asset) {
        // Kalau sudah login, arahkan ke halaman authenticated (show.blade)
        if (Auth::check()) {
            return redirect()->route('asset-library.show', $asset->code);
        }
        // Kalau belum login, arahkan ke halaman publik (public-show.blade)
        return redirect()->route('asset.public', $asset->code);
    }

    if (Auth::check()) {
        return redirect()->route('asset-library.index')->with('error', 'Asset tidak ditemukan dengan kode: ' . $qrCode);
    }
    return redirect()->route('asset.public', 'notfound')->with('error', 'Asset tidak ditemukan');
}

public function store(Request $request)
{
    dd($request->all()); 
}

public function publicShow($code)
{
    $asset = Asset::where('code', $code)->firstOrFail();
    
    // Load relasi loans dengan user untuk riwayat penggunaan
    $asset->load(['loans.user']);
    
    return view('asset-library.public-show', compact('asset'));
}

public function servePhoto($path)
{
    try {
        // Try to get file from RustFS
        if (Storage::disk('rustfs')->exists($path)) {
            $file = Storage::disk('rustfs')->get($path);
            $mimeType = Storage::disk('rustfs')->mimeType($path);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400');
        }
        
        // Fallback to local storage
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
        
        abort(404, 'Photo not found');
        
    } catch (\Exception $e) {
        // If RustFS fails, try local storage
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
        
        abort(404, 'Photo not found');
    }
}
}