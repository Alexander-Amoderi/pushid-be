<?php

namespace App\Http\Controllers;

use App\Models\Lobby; // JANGAN LUPA IMPORT MODELNYA
use Illuminate\Support\Str;
use App\Http\Requests\StoreLobbyRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LobbyResource;

class LobbyController extends Controller
{
    // [FUNGSI INDEX]
    public function index()
    {
        // Ambil semua data dari tabel lobbies
        //$lobbies = Lobby::all();
        // Eager Load relasi user untuk mendapatkan nama creator
        $lobbies = Lobby::with('user')->get();

        // Return data dalam format JSON
        return response()->json([
            'status' => 'success',
            //'data' => $lobbies
            'data' => LobbyResource::collection($lobbies)
        ]);
    }
    
    // [FUNGSI SHOW]
    public function show(string $slug)
    {
        // Cari data lobby berdasarkan slug. Menggunakan firstOrFail() akan melempar 404 jika tidak ditemukan.
        //$lobby = Lobby::where('slug', $slug)->first();
        //$lobby = Lobby::where('slug', $slug)->firstOrFail();
        $lobby = Lobby::with('user')->where('slug', $slug)->firstOrFail();

        // Cek jika data tidak ditemukan
        if (!$lobby) {
             return response()->json([
                'status' => 'error',
                'message' => 'Lobby not found'
            ], 404);
        }

        // Return data tunggal dalam format JSON
        return response()->json([
            'status' => 'success',
            //'data' => $lobby
            'data' => new LobbyResource($lobby)
        ]);
    }

    // [FUNGSI STORE]
    //public function store(StoreLobbyRequest $request) // Gunakan StoreLobbyRequest
    //{
    //    // Data sudah divalidasi oleh StoreLobbyRequest
    //    $validatedData = $request->validated();
//
    //    // [GENERATE SLUG OTOMATIS]
    //    // Buat slug dari judul
    //    $slug = Str::slug($validatedData['title']);
    //    $validatedData['slug'] = $slug;
//
    //    // Pastikan slug unik (jika ada judul yang sama)
    //    // Cek apakah slug sudah ada, jika ya, tambahkan angka di belakangnya
    //    $originalSlug = $slug;
    //    $count = 1;
    //    while (Lobby::where('slug', $slug)->exists()) {
    //        $slug = $originalSlug . '-' . $count++;
    //    }
    //    $validatedData['slug'] = $slug;
//
    //    // Simpan data ke database
    //    $lobby = Lobby::create($validatedData);
//
    //    // Return response sukses
    //    return response()->json([
    //        'status' => 'success',
    //        'message' => 'Lobby berhasil diposting!',
    //        'data' => $lobby
    //    ], 201); // 201 Created
    //}
    
    public function store(StoreLobbyRequest $request)
    {
        $validatedData = $request->validated();

        // **PERUBAHAN PENTING:**
        // 1. Hapus user_id dari data request yang divalidasi (jika ada)
        //unset($validatedData['user_id']); 
        $validatedData['user_id'] = Auth::id();

        // 2. Isi user_id secara otomatis dengan ID user yang sedang login
        $validatedData['user_id'] = Auth::id(); 

        // [Logika pembuatan slug tetap sama]
        $slug = Str::slug($validatedData['title']);
        // ... (Logika pengecekan keunikan slug)

        $validatedData['slug'] = $slug;

        $lobby = Lobby::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Lobby berhasil diposting!',
            'data' => $lobby
        ], 201);
    } 
    
    // [FUNGSI DESTROY]
    public function destroy(string $slug)
    {
        // 1. Cari Lobby
        $lobby = Lobby::where('slug', $slug)->first();

        if (!$lobby) {
            return response()->json(['message' => 'Lobby not found'], 404);
        }

        // 2. [LOGIC OTORISASI] Cek Kepemilikan
        if ($lobby->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden. Anda tidak memiliki izin untuk menghapus lobi ini.'
            ], 403); // 403 Forbidden
        }

        $lobby->delete();

        return response()->json(['message' => 'Lobby berhasil dihapus'], 200);
    }
}
