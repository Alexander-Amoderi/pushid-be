<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id', 
        'lobby_id', 
        'reason', 
        'description', 
        'status', 
        'priority'
    ];

    /**
     * Relasi ke User yang melaporkan (Reporter)
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Relasi ke Lobby yang dilaporkan
     */
    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }
}
