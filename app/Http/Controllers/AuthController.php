<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Logout from all other devices.
     */
    public function logoutAllDevices(Request $request)
    {
        $user = Auth::user();
        
        // Get current session ID to keep it
        $currentSessionId = session()->getId();
        
        // Delete all sessions for this user except current
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return redirect()->back()
            ->with('success', 'Semua perangkat lain telah berhasil logout.');
    }
}
