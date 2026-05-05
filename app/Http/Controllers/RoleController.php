<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // ===============================
    // INDEX (SEARCH + SORT + PAGINATION)
    // ===============================
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        // SEARCH
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // SORT
       switch ($request->sort) {
    case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;

    case 'newest':
        $query->orderBy('created_at', 'desc');
        break;

    case 'za':
        $query->orderByRaw('LOWER(name) DESC');
        break;

    case 'az':
    default:
        $query->orderByRaw('LOWER(name) ASC');
        break;
}
        

        $roles = $query->paginate(5)->withQueryString();

        return view('roles.index', compact('roles'));
    }

    // ===============================
    // STORE (TAMBAH ROLE)
    // ===============================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // CEK DUPLIKAT (CASE-INSENSITIVE)
        $exists = Role::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Role sudah ada!'])
                ->withInput();
        }

        Role::create([
            'name' => ucfirst(strtolower($request->name))
        ]);

        return back()->with('success', 'Role berhasil ditambahkan');
    }

    // ===============================
    // UPDATE (EDIT ROLE)
    // ===============================
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ]);

        // CEK DUPLIKAT SAAT EDIT
        $exists = Role::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Role sudah ada!'])
                ->withInput();
        }

        $role->update([
            'name' => ucfirst(strtolower($request->name))
        ]);

        return back()->with('success', 'Role berhasil diupdate');
    }

    // ===============================
    // DELETE 1 DATA
    // ===============================
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name == 'Admin') {
            return back()->with('error', 'Role Admin tidak bisa dihapus');
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus');
    }

    // ===============================
    // DELETE SEMUA (KECUALI ADMIN)
    // ===============================
    public function deleteAll()
    {
        Role::where('name', '!=', 'Admin')->delete();

        return back()->with('success', 'Semua role berhasil dihapus');
    }

    // ===============================
    // BULK DELETE (HAPUS TERPILIH)
    // ===============================
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data dipilih'
            ]);
        }

        Role::whereIn('id', $ids)
            ->where('name', '!=', 'Admin')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus'
        ]);
    }
}