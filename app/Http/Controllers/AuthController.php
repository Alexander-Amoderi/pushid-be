<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // [FUNGSI REGISTER]
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Buat Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Kirim Respon Sukses
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
            'token' => $token
        ], 201);
    }
    
    // [FUNGSI LOGIN]
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 2. Cek Kredensial (Email)
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Melempar error validasi yang akan menghasilkan status 422
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        // 3. Hapus Token Lama (Opsional, untuk keamanan)
        $user->tokens()->delete();

        // 4. Buat Token Baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Kirim Respon Sukses
        return response()->json([
            'message' => 'Login successful!',
            // Sesuai kebutuhan front-end: mengirim user data dan token
            'user' => $user, 
            'token' => $token
        ], 200);
    }

    // [FUNGSI LOGOUT]
    public function logout(Request $request)
    {
        // Hapus token yang digunakan untuk autentikasi saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful!'
        ], 200);
    }

    // [FUNGSI LOGIN ADMIN]
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Validasi: Cek apakah user ada, password cocok, dan role adalah admin
        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'admin') {
            return response()->json([
                'message' => 'Login gagal. Email/Password salah atau Anda bukan Admin.'
            ], 401);
        }

        // Buat Token
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Admin Berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }
}