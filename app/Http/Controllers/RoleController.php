<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
   public function index(Request $request)
{
    $roles = \App\Models\Role::withCount('users')
        ->orderByRaw("CASE WHEN name = 'Admin' THEN 0 ELSE 1 END")
        ->orderBy('name')
        ->paginate(5);

    return view('roles.index', compact('roles'));
}
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return back()->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return back()->with('success', 'Role berhasil diupdate');
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return back()->with('success', 'Role berhasil dihapus');
    }
}