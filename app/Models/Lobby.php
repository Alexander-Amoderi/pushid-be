<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lobby extends Model
{
    use HasFactory, SoftDeletes; // Tambahkan SoftDeletes agar fitur hapus admin lebih aman

    protected $fillable = [
        'game_name',
        'title',
        'description',
        'rank', 
        'link', 
        'status',
        'user_id',
        'slug'    
    ];

    /**
     * Menambahkan atribut virtual agar otomatis muncul saat data ditarik ke API
     * Atribut ini dibutuhkan oleh tabel Manage Lobbies
     */
    protected $appends = ['players_count', 'reports_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Reports
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * ACCESSOR: Menghitung jumlah pemain
     * Untuk MVP, kita kembalikan nilai 1 (sang pembuat). 
     * Ke depannya ini bisa dihitung dari tabel partisipan.
     */
    public function getPlayersCountAttribute()
    {
        return 1; 
    }

    /**
     * ACCESSOR: Menghitung jumlah laporan khusus lobby ini
     * Digunakan untuk kolom 'Reports' di tabel admin
     */
    public function getReportsCountAttribute()
    {
        return $this->reports()->count();
    }
}