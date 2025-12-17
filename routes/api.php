<?php

use Illuminate\Http\Request;
use App\Http\Controllers\LobbyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- AUTH ROUTES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// [ROUTE GET ALL] GET /lobbies
Route::get('/lobbies', [LobbyController::class, 'index']);
// [ROUTE GET DETAIL] GET /lobbies/{slug}
Route::get('/lobbies/{slug}', [LobbyController::class, 'show']);

// Route yang DILINDUNGI (hanya untuk user yang login)
Route::middleware('auth:sanctum')->group(function () {
    // [ROUTE CREATE] POST /lobbies
    Route::post('/lobbies', [LobbyController::class, 'store']);
    // [PROTECT DELETE] DELETE /lobbies/{slug}
    Route::delete('/lobbies/{slug}', [LobbyController::class, 'destroy']);
    // [PROTECT UPDATE] UPDATE /lobbies/{slug}
    Route::put('/lobbies/{slug}', [LobbyController::class, 'update']);
    // [ROUTE LOGOUT] POST /logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tambahkan route untuk Update dan Delete di sini (akan dibuat di LEVEL 5)
    // Route::put('/lobbies/{slug}', [LobbyController::class, 'update']);
    // Route::delete('/lobbies/{slug}', [LobbyController::class, 'destroy']);
});
