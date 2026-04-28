<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with pagination (15 per page).
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Sort by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage with database transaction.
     */
    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('User creation request:', $request->all());
        
        $validated = $request->validate([
            'name' => [
    'required', 
    'string', 
    'max:255', 
    function ($attribute, $value, $fail) {
        $exists = \App\Models\User::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
        if ($exists) {
            $fail('Nama sudah digunakan');
        }
    }
],
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'name.unique' => 'Nama sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role wajib dipilih',
            'role_id.exists' => 'Role yang dipilih tidak valid',
            'is_active.required' => 'Status aktif wajib dipilih',
            'is_active.boolean' => 'Status aktif harus berupa boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'is_active' => $request->is_active,
            ]);

            DB::commit();
            
            \Log::info('User created successfully:', ['user_id' => $user->id, 'email' => $user->email]);
            
            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User creation failed:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get user data for editing (API endpoint).
     */
    public function edit(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Update the specified user in storage with database transaction.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => [
    'required', 
    'string', 
    'max:255', 
    function ($attribute, $value, $fail) use ($user) {
        $exists = \App\Models\User::whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->where('id', '!=', $user->id)
            ->exists();
        if ($exists) {
            $fail('Nama sudah digunakan');
        }
    }
],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
            ];
            
            // Update password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            
            $user->update($updateData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate user'
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage with safety checks.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus akun sendiri'
            ], 403);
        }
        
        // Check if user has loan history
        $hasLoans = Loan::where('user_id', $user->id)->exists();
        if ($hasLoans) {
            return response()->json([
                'success' => false,
                'message' => 'User memiliki riwayat peminjaman. Nonaktifkan akun saja.'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $user->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user'
            ], 500);
        }
    }

    /**
     * Reset user password to default with security check.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent resetting self password (optional - can be removed if allowed)
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan halaman profile untuk reset password sendiri'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $user->update([
                'password' => Hash::make('password123')
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset ke: password123'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset password'
            ], 500);
        }
    }

    /**
     * Toggle user active status with safety checks.
     */
    public function toggle(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deactivating self
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menonaktifkan akun sendiri'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $newStatus = !$user->is_active;
            $user->update(['is_active' => $newStatus]);
            
            DB::commit();
            
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "User berhasil {$statusText}",
                'is_active' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status user'
            ], 500);
        }
    }

    /**
     * Get user loan history for history modal.
     */
    public function getHistory($id)
    {
        $user = User::findOrFail($id);
        
        // Get active loans
        $activeLoans = Loan::with(['assets'])
            ->where('user_id', $id)
            ->where('status', 'dipinjam')
            ->get()
            ->map(function($loan) {
                return [
                    'asset_name' => $loan->assets->first()->name ?? 'Unknown',
                    'asset_code' => $loan->assets->first()->code ?? 'Unknown',
                    'borrow_date' => \Carbon\Carbon::parse($loan->borrow_date)->translatedFormat('d M Y'),
                    'return_date' => $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d M Y') : '-',
                ];
            });
        
        // Get past history
        $pastHistory = Loan::with(['assets'])
            ->where('user_id', $id)
            ->where('status', '!=', 'dipinjam')
            ->get()
            ->map(function($loan) {
                return [
                    'asset_name' => $loan->assets->first()->name ?? 'Unknown',
                    'asset_code' => $loan->assets->first()->code ?? 'Unknown',
                    'borrow_date' => \Carbon\Carbon::parse($loan->borrow_date)->translatedFormat('d M Y'),
                    'return_date' => $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d M Y') : '-',
                ];
            });
        
        return response()->json([
            'success' => true,
            'user_name' => $user->name,
            'active_loans' => $activeLoans,
            'past_history' => $pastHistory
        ]);
    }
}