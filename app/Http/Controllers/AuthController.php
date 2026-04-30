<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Process login.
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah']);
        }

        // Check if user is active
        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Akun anda nonaktif']);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset sudah dikirim!')
            : back()->withErrors(['email' => 'Email tidak ditemukan']);
    }

    /**
     * Show reset password form.
     */
    public function showResetPassword(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Process password reset.
     */
    public function resetPassword(Request $request)
    {
        // Debug: Log semua input
        Log::info('Reset password attempt:', [
            'email' => $request->email,
            'token' => $request->token,
            'password_length' => strlen($request->password),
            'password_confirmed' => $request->password === $request->password_confirmation
        ]);

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Debug: Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            Log::error('User not found for email:', ['email' => $request->email]);
            return back()->withErrors(['email' => 'User tidak ditemukan']);
        }
        
        Log::info('User found:', ['user_id' => $user->id, 'user_email' => $user->email]);

        // Cek token validitas (sederhana - bypass untuk testing)
        // TODO: Implement proper token validation
        
        // Update password langsung
        try {
            $user->password = Hash::make($request->password);
            $user->save();
            
            Log::info('Password updated successfully for user:', ['user_id' => $user->id]);
            
            // Logout user yang sedang login (jika ada)
            Auth::logout();
            
            // Invalidate semua session
            session()->invalidate();
            session()->regenerateToken();
            
            return redirect('/login')->with('status', 'Password berhasil diubah! Silakan login kembali.');
            
        } catch (\Exception $e) {
            Log::error('Error updating password:', ['error' => $e->getMessage()]);
            return back()->withErrors(['email' => 'Gagal mengubah password: ' . $e->getMessage()]);
        }
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        // Invalidate current session
        Auth::logout();
        
        // Clear all session data
        session()->invalidate();
        session()->regenerateToken();
        
        // Clear cookies
        Cookie::queue(Cookie::forget('laravel_session'));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        
        return redirect('/login')->with('status', 'Anda telah berhasil logout.');
    }

    }
