<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    // =====================
    // INDEX (LIST DATA)
    // =====================
    public function index()
    {
        // FIX UTAMA: harus paginate, bukan get/all
        $assets = Asset::paginate(10);

        return view('assets.index', compact('assets'));
    }


    // =====================
    // STORE (TAMBAH DATA)
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'code' => 'required|unique:assets,code',
            'stock' => 'required|numeric',
            'condition' => 'required',
            'status' => 'required',
        ]);

        Asset::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'description' => $request->description,
            'stock' => $request->stock,
            'condition' => $request->condition,
            'status' => $request->status,
        ]);

        return redirect()->route('assets')->with('success', 'Asset berhasil ditambahkan');
    }


    // =====================
    // UPDATE (EDIT DATA)
    // =====================
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'code' => 'required|unique:assets,code,' . $id,
            'stock' => 'required|numeric',
            'condition' => 'required',
            'status' => 'required',
        ]);

        $asset->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'description' => $request->description,
            'stock' => $request->stock,
            'condition' => $request->condition,
            'status' => $request->status,
        ]);

        return redirect()->route('assets')->with('success', 'Asset berhasil diupdate');
    }


    // =====================
    // DELETE (HAPUS DATA)
    // =====================
    public function destroy($id)
    {
        Asset::findOrFail($id)->delete();

        return redirect()->route('assets')->with('success', 'Asset berhasil dihapus');
    }
}