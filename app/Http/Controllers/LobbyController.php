<?php

namespace App\Http\Controllers;

use App\Models\Lobby;
use Illuminate\Support\Str;
use App\Http\Requests\StoreLobbyRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LobbyResource;
use App\Models\Report;
use Illuminate\Http\Request;

class LobbyController extends Controller
{
    /**
     * GET /api/lobbies
     * Mengambil semua lobby dari database (PUBLIC - tidak perlu auth)
     * Ini adalah satu-satunya sumber kebenaran untuk daftar lobby
     */
    public function index()
    {
        // Ambil semua lobby dengan eager loading relasi user
        // Urutkan dari yang terbaru agar lobby baru muncul di atas
        $lobbies = Lobby::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => LobbyResource::collection($lobbies)
        ]);
    }

    /**
     * GET /api/lobbies/{slug}
     * Mengambil detail satu lobby berdasarkan slug
     */
    public function show(string $slug)
    {
        $lobby = Lobby::with('user')->where('slug', $slug)->first();

        if (!$lobby) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lobby tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new LobbyResource($lobby)
        ]);
    }

    /**
     * POST /api/lobbies (PROTECTED - wajib auth)
     * Membuat lobby baru
     * 
     * ARSITEKTUR PENTING:
     * - user_id diambil dari TOKEN, BUKAN dari request body
     * - Frontend tidak perlu dan tidak boleh mengirim user_id
     * - Backend adalah satu-satunya sumber kebenaran
     */
    public function store(StoreLobbyRequest $request)
    {
        // 1. Ambil data yang sudah divalidasi
        $validatedData = $request->validated();

        // 2. AMBIL user_id DARI TOKEN (Auth::id())
        //    Ini adalah cara yang BENAR dan AMAN
        //    Frontend tidak bisa memalsukan user_id
        $validatedData['user_id'] = Auth::id();

        // 3. Generate slug yang unik dari title
        $baseSlug = Str::slug($validatedData['title']);
        $slug = $baseSlug;
        $counter = 1;

        // Pastikan slug unik
        while (Lobby::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $validatedData['slug'] = $slug;

        // 4. Simpan ke database
        $lobby = Lobby::create($validatedData);

        // 5. Load relasi user untuk response yang lengkap
        $lobby->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Lobby berhasil dibuat!',
            'data' => new LobbyResource($lobby)
        ], 201);
    }

    /**
     * PUT /api/lobbies/{slug} (PROTECTED)
     * Update lobby (hanya pemilik yang bisa update)
     */
    public function update(Request $request, string $slug)
    {
        // 1. Cari lobby
        $lobby = Lobby::where('slug', $slug)->first();

        if (!$lobby) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lobby tidak ditemukan'
            ], 404);
        }

        // 2. Otorisasi: hanya pemilik yang bisa update
        if ($lobby->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden. Anda tidak memiliki izin untuk mengubah lobby ini.'
            ], 403);
        }

        // 3. Validasi data update
        $validatedData = $request->validate([
            'game_name' => ['sometimes', 'string', 'max:255'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'rank' => ['sometimes', 'string', 'max:100'],
            'link' => ['sometimes', 'url', 'max:255'],
        ]);

        // 4. Update slug jika title berubah
        if (isset($validatedData['title']) && $validatedData['title'] !== $lobby->title) {
            $baseSlug = Str::slug($validatedData['title']);
            $slug = $baseSlug;
            $counter = 1;

            while (Lobby::where('slug', $slug)->where('id', '!=', $lobby->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validatedData['slug'] = $slug;
        }

        // 5. Update lobby
        $lobby->update($validatedData);
        $lobby->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Lobby berhasil diupdate!',
            'data' => new LobbyResource($lobby)
        ]);
    }

    /**
     * DELETE /api/lobbies/{slug} (PROTECTED)
     * Hapus lobby (hanya pemilik yang bisa hapus)
     */
    public function destroy(string $slug)
    {
        // 1. Cari Lobby
        $lobby = Lobby::where('slug', $slug)->first();

        if (!$lobby) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lobby tidak ditemukan'
            ], 404);
        }

        // 2. Otorisasi: hanya pemilik yang bisa hapus
        if ($lobby->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden. Anda tidak memiliki izin untuk menghapus lobby ini.'
            ], 403);
        }

        // 3. Hapus lobby
        $lobby->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lobby berhasil dihapus'
        ]);
    }

    /**
     * POST /api/lobbies/{id}/report (PROTECTED)
     * Melaporkan lobby yang melanggar aturan
     */
    public function reportLobby(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'description' => 'nullable|string'
        ]);

        // Cek apakah lobby exists
        $lobby = Lobby::find($id);
        if (!$lobby) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lobby tidak ditemukan'
            ], 404);
        }

        Report::create([
            'reporter_id' => Auth::id(),
            'lobby_id' => $id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil disimpan'
        ], 201);
    }
}
