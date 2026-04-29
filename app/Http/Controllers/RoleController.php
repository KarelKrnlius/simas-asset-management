<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->paginate(5);
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ]);

        Role::findOrFail($id)->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Role berhasil diupdate');
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return back()->with('success', 'Role berhasil dihapus');
    }
}