<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // =====================
    // INDEX (LIST USER)
    // =====================
    public function index()
    {
        $users = User::paginate(10); // ✅ FIX UTAMA

        return view('users.index', compact('users'));
    }

    // =====================
    // STORE (TAMBAH USER)
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users')->with('success', 'User berhasil ditambahkan');
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users')->with('success', 'User berhasil diupdate');
    }

    // =====================
    // DELETE
    // =====================
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('users')->with('success', 'User berhasil dihapus');
    }
}