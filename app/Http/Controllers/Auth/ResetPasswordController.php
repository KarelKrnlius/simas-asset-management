<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    // Method untuk menampilkan halaman form (View yang kita buat tadi)
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Method untuk memproses update password (Logika nomor 2)
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // Otomatis cek field 'password_confirmation'
        ]);

        // Berdasarkan ERD: Update password di tabel users
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => now(), // Sesuai kolom di ERD
            ]);

        // Sesuai Flow: Setelah sukses, kembali ke Login
        return redirect()->route('login')->with('status', 'Password berhasil diperbarui!');
    }
}