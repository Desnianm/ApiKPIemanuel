<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET semua user (owner only)
    public function index()
    {
        $users = User::with('unitBisnis')->get();

        return response()->json([
            'data' => $users->makeHidden(['password', 'remember_token'])
        ], 200);
    }

    // GET detail 1 user
    public function show($id)
    {
        $user = User::with('unitBisnis')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $user->makeHidden(['password', 'remember_token'])
        ], 200);
    }

    // POST buat user baru (owner only)
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8',
            'role'           => 'required|in:owner,manajer,karyawan',
            'unit_bisnis_id' => 'nullable|exists:unit_bisnis,id',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'unit_bisnis_id' => $request->unit_bisnis_id,
            'is_active'      => true,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'data'    => $user->makeHidden(['password', 'remember_token'])
        ], 201);
    }

    // PUT update user (owner only)
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'name'           => 'sometimes|string|max:100',
            'email'          => 'sometimes|email|unique:users,email,' . $id,
            'password'       => 'sometimes|min:8',
            'role'           => 'sometimes|in:owner,manajer,karyawan',
            'unit_bisnis_id' => 'nullable|exists:unit_bisnis,id',
            'is_active'      => 'sometimes|boolean',
        ]);

        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User berhasil diupdate.',
            'data'    => $user->makeHidden(['password', 'remember_token'])
        ], 200);
    }

    // DELETE user (owner only) — SOFT DELETE
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Cegah owner menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Tidak bisa menghapus akun sendiri.'
            ], 422);
        }

        // Soft delete — akun tidak bisa login lagi
        // tapi riwayat form_submissions tetap utuh
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus. Riwayat data tetap tersimpan.'
        ], 200);
    }

    // PATCH toggle aktif/nonaktif user (owner only)
    public function toggle($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Cegah nonaktifkan akun sendiri
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Tidak bisa menonaktifkan akun sendiri.'
            ], 422);
        }

        // Cegah nonaktifkan sesama owner
        if ($user->role === 'owner') {
            return response()->json([
                'message' => 'Tidak bisa menonaktifkan akun owner.'
            ], 422);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'message' => "User berhasil {$status}.",
            'data'    => $user->makeHidden(['password', 'remember_token'])
        ], 200);
    }
}