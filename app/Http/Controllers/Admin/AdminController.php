<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lobby;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * 1. USER MANAGEMENT LOGIC
     */
    public function indexUsers()
    {
        $users = User::all()->map(function($user) {
            return [
                'id' => $user->id,
                'username' => $user->name, // Mapping ke username sesuai desain
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role,
                'joinDate' => $user->created_at->format('Y-m-d'),
                'reportsCount' => $user->reports_count,
                'banUntil' => $user->ban_until ? $user->ban_until->format('Y-m-d') : null,
                'adminNote' => $user->admin_note,
            ];
        });
        
        return response()->json($users);
    }

    public function toggleUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->status === 'active') {
            $duration = $request->input('duration'); 
            $reason = $request->input('reason'); 

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
            $user->status = 'active';
            $user->ban_until = null;
            $user->admin_note = null;
        }

        $user->save();
        return response()->json(['message' => "Status user {$user->name} diperbarui"]);
    }

    // Fungsi tambahan untuk tombol "Hapus Permanen" di UsersManagement
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admin tidak bisa dihapus'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'Akun berhasil dihapus permanen']);
    }

    /**
     * 2. LOBBY MANAGEMENT LOGIC
     */
    public function indexLobbies()
    {
        // Sesuaikan dengan kolom tabel: Lobby, Game, Creator, Status, Players, Reports
        $lobbies = Lobby::with('user')->get()->map(function($lobby) {
            return [
                'id' => $lobby->id,
                'title' => $lobby->title,
                'rank' => $lobby->rank,
                'game' => strtoupper($lobby->game_name), // HOK, MLBB, dll
                'creator' => $lobby->user ? $lobby->user->name : 'System',
                'status' => $lobby->status, // 'active' atau 'inactive'
                'players' => $lobby->players_count, // Dari accessor model
                'reports' => $lobby->reports_count, // Dari accessor model
            ];
        });
        
        return response()->json($lobbies);
    }

    public function deleteLobby($id)
    {
        $lobby = Lobby::findOrFail($id);
        $lobby->delete(); // Menggunakan SoftDeletes jika diaktifkan di model
        return response()->json(['message' => 'Lobby berhasil dihapus']);
    }

    /**
     * 3. REPORT MANAGEMENT LOGIC
     */
    public function indexReports()
    {
        // Untuk Dashboard Moderasi
        $reports = Report::with(['reporter', 'lobby.user'])->latest()->get()->map(function($report) {
            return [
                'id' => $report->id,
                'reporter' => $report->reporter->name,
                'reportedUser' => $report->lobby->user->name ?? 'Unknown',
                'lobbyTitle' => $report->lobby->title ?? 'Deleted Lobby',
                'reason' => $report->reason,
                'detail' => $report->description,
                'status' => $report->status,
                'date' => $report->created_at->format('Y-m-d H:i')
            ];
        });
        return response()->json($reports);
    }

    // Menangani aksi dari modal moderasi
    public function updateReportStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->status = 'resolved';
        $report->save();

        return response()->json(['message' => 'Laporan telah diselesaikan']);
    }

    /**
     * 4. ANALYTICS LOGIC
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