<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/lobbies', [LobbyController::class, 'index']);
Route::get('/lobbies/{slug}', [LobbyController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected User Routes (Hanya User Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/lobbies', [LobbyController::class, 'store']);
    Route::delete('/lobbies/{slug}', [LobbyController::class, 'destroy']);
    Route::put('/lobbies/{slug}', [LobbyController::class, 'update']);
    Route::post('/lobbies/{id}/report', [LobbyController::class, 'reportLobby']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    
    // Dashboard Stats
    Route::get('/analytics', [AdminController::class, 'getAnalytics']);
    
    // User Management
    Route::get('/users', [AdminController::class, 'indexUsers']);
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    
    // Lobby Management
    Route::get('/lobbies', [AdminController::class, 'indexLobbies']);
    Route::delete('/lobbies/{id}', [AdminController::class, 'deleteLobby']);

    // Report Management
    Route::get('/reports', [AdminController::class, 'indexReports']);
    Route::patch('/reports/{id}/resolve', [AdminController::class, 'updateReportStatus']); // TAMBAHAN: Selesaikan laporan
});