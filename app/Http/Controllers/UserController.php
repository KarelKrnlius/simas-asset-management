<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loan;
use App\Models\Role;
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
        $query = User::with('role');
        
        // Search functionality - search by name, email, role, and status
        if ($request->has('search') && $request->search) {
            $searchTerm = strtolower($request->search);
            $query->where(function($q) use ($searchTerm) {
                // Check if searching specifically for status
                if ($searchTerm === 'aktif') {
                    $q->where('is_active', true);
                } elseif ($searchTerm === 'nonaktif') {
                    $q->where('is_active', false);
                } else {
                    // General search by name, email, role
                    $q->where('name', 'ILIKE', '%' . $searchTerm . '%')
                      ->orWhere('email', 'ILIKE', '%' . $searchTerm . '%')
                      ->orWhereHas('role', function($roleQ) use ($searchTerm) {
                          $roleQ->where('name', 'ILIKE', '%' . $searchTerm . '%');
                      });
                }
            });
        }
        
        // Sorting functionality
        $sortBy = $request->get('sort_by');
        $order = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'created_at':
                $query->orderBy('created_at', $order);
                break;
            case 'name':
                $query->orderBy('name', $order);
                break;
            default:
                if ($sortBy && str_starts_with($sortBy, 'role_')) {
                    $roleId = str_replace('role_', '', $sortBy);
                    $query->where('role_id', $roleId);
                }
                $query->latest();
                break;
        }
        
        $users = $query->paginate(15);
        $roles = Role::orderBy('name')->get();
        
        // Append search and sort parameters to pagination links
        $users->appends(request()->only(['search', 'sort_by', 'order']));
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
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
        $user->load('role');
        $roles = Role::orderBy('name')->get();
        return response()->json([
            'success' => true,
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified user in storage with database transaction.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent editing self
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa mengedit akun sendiri'
                ], 403);
            }

            // Check if this is a status change request
            if ($request->has('status')) {
                $newStatus = $request->status === 'aktif';
                
                DB::beginTransaction();
                $user->update(['is_active' => $newStatus]);
                DB::commit();

                // Jika dinonaktifkan, paksa logout user tersebut
                if (!$newStatus) {
                    DB::table('sessions')->where('user_id', $id)->delete();
                }
                
                $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
                
                return response()->json([
                    'success' => true,
                    'message' => "User berhasil {$statusText}"
                ]);
            }

            // Validate request data for regular update
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'role_id' => 'required|exists:roles,id',
                'password' => 'nullable|string|min:6',
                'password_confirmation' => 'required_with:password|same:password'
            ], [
                'name.required' => 'Nama harus diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.max' => 'Email maksimal 255 karakter',
                'email.unique' => 'Email sudah terdaftar',
                'role_id.required' => 'Role harus dipilih',
                'role_id.exists' => 'Role tidak valid',
                'password.min' => 'Password minimal 6 karakter',
                'password_confirmation.required_with' => 'Konfirmasi password harus diisi',
                'password_confirmation.same' => 'Konfirmasi password tidak sama'
            ]);

            DB::beginTransaction();

            // Simpan role lama sebelum diupdate
            $oldRoleId = $user->role_id;

            // Update user data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id']
            ];

            // Update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            DB::commit();

            // Jika role diubah, paksa logout user tersebut
            // dengan menghapus semua session aktifnya
            if ((int)$validated['role_id'] !== (int)$oldRoleId) {
                DB::table('sessions')->where('user_id', $id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()
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
     * Get loan history for a specific user (API endpoint for modal).
     */
    public function history($id)
    {
        $user = User::findOrFail($id);

        $activeLoans = Loan::with(['assets.category'])
            ->where('user_id', $id)
            ->where('status', '!=', 'dikembalikan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($loan) {
                return $loan->assets->map(function ($asset) use ($loan) {
                    return [
                        'loan_id'    => $loan->id,
                        'loan_code'  => $loan->loan_code,
                        'asset_name' => $asset->name,
                        'asset_code' => $asset->code ?? '-',
                        'borrow_date'=> $loan->borrow_date
                            ? \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y')
                            : '-',
                        'status'     => $loan->status,
                    ];
                });
            })->flatten(1)->values();

        $pastHistory = Loan::with(['assets.category'])
            ->where('user_id', $id)
            ->where('status', 'dikembalikan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($loan) {
                return $loan->assets->map(function ($asset) use ($loan) {
                    $condition = $asset->pivot->condition ?? null;
                    return [
                        'loan_id'     => $loan->id,
                        'loan_code'   => $loan->loan_code,
                        'asset_name'  => $asset->name,
                        'asset_code'  => $asset->code ?? '-',
                        'borrow_date' => $loan->borrow_date
                            ? \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y')
                            : '-',
                        'return_date' => $loan->return_date
                            ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y')
                            : '-',
                        'condition'   => $condition ?? 'baik',
                        'status'      => $loan->status,
                    ];
                });
            })->flatten(1)->values();

        return response()->json([
            'user_name'    => $user->name,
            'active_loans' => $activeLoans,
            'past_history' => $pastHistory,
        ]);
    }

    /**
     * Change user status with confirmation.
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent changing self status
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa mengubah status akun sendiri'
                ], 403);
            }

            $newStatus = $request->status === 'aktif';
            
            DB::beginTransaction();
            $user->update(['is_active' => $newStatus]);
            DB::commit();

            // Jika dinonaktifkan, paksa logout user tersebut
            if (!$newStatus) {
                DB::table('sessions')->where('user_id', $id)->delete();
            }
            
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "User berhasil {$statusText}"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status user'
            ], 500);
        }
    }

    }