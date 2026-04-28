<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update name dan email
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password hanya jika input tidak kosong
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Logout user from all devices
     */
    public function logoutAllDevices()
    {
        $user = Auth::user();
        
        // Revoke all tokens for the user (if using API tokens)
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        
        // Invalidate all sessions by updating password hash
        // This will force logout from all devices
        $user->password = Hash::make($user->password);
        $user->save();
        
        // Logout current session
        Auth::logout();
        
        // Invalidate current session
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah keluar dari semua perangkat!');
    }
}
