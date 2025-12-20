<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'ban_until',
        'admin_note',
        'last_active_at'
    ];

    // Mengizinkan penambahan data kustom saat data user dikirim ke API
    protected $appends = ['reports_count'];

    // Aksesor untuk menghitung jumlah laporan yang dibuat oleh user
    public function getReportsCountAttribute()
    {
        // Menghitung total laporan yang masuk ke semua lobby milik user ini
        return Report::whereHas('lobby', function($query) {
            $query->where('user_id', $this->id);
        })->count();
    }

    // Relasi untuk menghitung jumlah lobby di UsersManagement
    public function lobbies()
    {
        return $this->hasMany(Lobby::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reportsSent() {
        return $this->hasMany(Report::class, 'reporter_id');
    }
}
