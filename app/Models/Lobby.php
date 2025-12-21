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
        'rank',
        'title',
        'slug',
        'description',
        'link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}