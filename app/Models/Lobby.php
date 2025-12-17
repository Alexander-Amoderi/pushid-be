<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lobby extends Model
{
    use HasFactory;

    // Tentukan kolom-kolom yang aman untuk diisi secara massal
    protected $fillable = [
        'user_id',
        'game_name',
        'title',
        'slug',
        'description',
        'contact_info',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}