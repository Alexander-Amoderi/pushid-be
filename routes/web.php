<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;

Route::get('/', function () {
    return view('welcome');
});
// [ROUTE SITEMAP] GET /sitemap
Route::get('/sitemap', [SitemapController::class, 'index']);
