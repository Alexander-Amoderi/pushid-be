<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lobby;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * 1. USER MANAGEMENT LOGIC
     * Mengambil daftar user dengan proteksi array kosong jika gagal
     */
    public function indexUsers()
    {
        try {
            $users = User::all()->map(function($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->name, // Sinkronisasi 'name' ke 'username' FE
                    'email' => $user->email,
                    'status' => $user->status, // active/banned
                    'role' => $user->role,
                    'joinDate' => $user->created_at->format('Y-m-d'),
                    'reportsCount' => $user->reports_count ?? 0, // Hitungan Pelanggaran
                    'banUntil' => $user->ban_until ? Carbon::parse($user->ban_until)->format('Y-m-d') : null,
                    'adminNote' => $user->admin_note,
                ];
            });
            return response()->json($users);
        } catch (\Exception $e) {
            // Mengembalikan array kosong agar FE .filter() tidak error
            return response()->json([], 200); 
        }
    }

    /**
     * Fitur Ban/Unban dengan durasi dan alasan kustom
     */
    public function toggleUserStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->status === 'active') {
                $duration = $request->input('duration', 'permanent'); 
                $reason = $request->input('reason', 'Pelanggaran aturan komunitas'); 

                $user->status = 'banned';
                $user->admin_note = $reason;

                if ($duration !== 'permanent') {
                    $days = match($duration) {
                        '1_day' => 1,
                        '3_days' => 3,
                        '1_week' => 7,
                        '1_month' => 30,
                        default => 0
                    };
                    $user->ban_until = Carbon::now()->addDays($days);
                } else {
                    $user->ban_until = null;
                }
            } else {
                // Proses Unban
                $user->status = 'active';
                $user->ban_until = null;
                $user->admin_note = null;
            }

            $user->save();
            return response()->json(['message' => "Status user {$user->name} berhasil diperbarui"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role === 'admin') {
                return response()->json(['message' => 'Admin utama tidak bisa dihapus'], 403);
            }
            $user->delete();
            return response()->json(['message' => 'Akun berhasil dihapus permanen']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus user'], 500);
        }
    }

    /**
     * 2. LOBBY MANAGEMENT LOGIC
     * Sinkronisasi data untuk tabel Manage Lobbies
     */
    public function indexLobbies()
    {
        try {
            $lobbies = Lobby::with('user')->get()->map(function($lobby) {
                return [
                    'id' => $lobby->id,
                    'title' => $lobby->title,
                    'rank' => $lobby->rank,
                    'game' => strtoupper($lobby->game_name), 
                    'creator' => $lobby->user ? $lobby->user->name : 'System',
                    'status' => $lobby->status, // active/inactive
                    'players' => $lobby->players_count ?? 1,
                    'reports' => $lobby->reports_count ?? 0,
                ];
            });
            return response()->json($lobbies);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function deleteLobby($id)
    {
        try {
            $lobby = Lobby::findOrFail($id);
            $lobby->delete(); 
            return response()->json(['message' => 'Lobby berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus lobby'], 500);
        }
    }

    /**
     * 3. REPORT MANAGEMENT LOGIC
     * Mengambil laporan untuk Dashboard Moderasi
     */
    public function indexReports()
    {
        try {
            $reports = Report::with(['reporter', 'lobby.user'])->latest()->get()->map(function($report) {
                return [
                    'id' => $report->id,
                    'reporter' => $report->reporter->name ?? 'User',
                    'reportedUser' => $report->lobby->user->name ?? 'Unknown',
                    'lobbyTitle' => $report->lobby->title ?? 'Deleted Lobby',
                    'reason' => $report->reason, // spam/harassment/dll
                    'detail' => $report->description,
                    'status' => $report->status, // pending/resolved
                    'date' => $report->created_at->format('Y-m-d H:i')
                ];
            });
            return response()->json($reports);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function updateReportStatus(Request $request, $id)
    {
        try {
            $report = Report::findOrFail($id);
            $report->status = 'resolved'; // Tandai selesai
            $report->save();
            return response()->json(['message' => 'Laporan telah diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui laporan'], 500);
        }
    }

    /**
     * 4. ANALYTICS LOGIC
     * Untuk mengisi angka statistik di Dashboard
     */
    public function getAnalytics()
    {
        return response()->json([
            'totalUsers' => User::count(),
            'activeLobbies' => Lobby::where('status', 'active')->count(),
            'totalReports' => Report::where('status', 'pending')->count(),
            'serverStatus' => 'online'
        ]);
    }
}