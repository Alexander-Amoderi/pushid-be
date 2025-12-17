<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lobby;

class SitemapController extends Controller
{
    public function index()
    {
        $lobbies = Lobby::all();
        $baseUrl = config('app.url'); // Mengambil base URL dari file .env

        // Definisikan header XML dan namespace
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Looping untuk setiap lobi
        foreach ($lobbies as $lobby) {
            $xml .= '<url>';
            // Ganti 'APP_URL' dengan base URL aplikasi front-end
            //$xml .= '<loc>' . env('APP_URL') . '/lobbies/' . $lobby->slug . '</loc>'; 
            $xml .= '<loc>' . $baseUrl . '/lobby/' . $lobby->slug . '</loc>';
            // Opsional: Tambahkan informasi waktu update
            $xml .= '<lastmod>' . $lobby->updated_at->tz('UTC')->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '<url>';
        $xml .= '<loc>' . $baseUrl . '</loc>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        $xml .= '</urlset>';

        // [PASTIKAN HEADER RESPONSE ADALAH text/xml]
        return response($xml, 200)
                ->header('Content-Type', 'text/xml');
    }
}
