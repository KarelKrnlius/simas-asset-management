<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(5);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        // ✅ VALIDASI
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',
            'role_id' => 'required'
        ], [
            'email.unique' => 'Email sudah digunakan!'
        ]);

        // ✅ PASSWORD DEFAULT
        $password = $request->password 
            ? Hash::make($request->password) 
            : Hash::make('password123');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
            'is_active' => true
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User berhasil dihapus');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('password123')
        ]);

        return back()->with('success', 'Password direset ke: password123');
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'Status user diubah');
    }
}