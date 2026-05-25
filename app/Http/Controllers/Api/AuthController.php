<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun kamu tidak aktif. Hubungi admin.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'unit_bisnis_id' => $user->unit_bisnis_id,
                'photo'          => $user->photo,
                'photo_url'      => $user->photo 
                                    ? asset('storage/' . $user->photo) 
                                    : asset('storage/photos/default.jpeg'),
            ],
        ], 200);
    }

    // logout 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ], 200);
    }

    // get profile user yang sedang login
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'unit_bisnis_id' => $user->unit_bisnis_id,
                'unit_bisnis'    => $user->unit_bisnis_id ? [
                    'id'   => $user->unitBisnis->id,
                    'nama' => $user->unitBisnis->nama,
                ] : null,
                'photo'          => $user->photo,
                'photo_url'      => $user->photo 
                                    ? asset('storage/' . $user->photo) 
                                    : asset('storage/photos/default.jpeg'),
                'is_active'      => $user->is_active,
            ]
        ], 200);
    }
}