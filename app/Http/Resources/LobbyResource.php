<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LobbyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            //'user_id' => $this->user_id,
            'game' => $this->game_name, // Di front-end disebut 'game'
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'rank' => $this->rank, // Kolom baru
            'link' => $this->link, // Kolom baru
            //'created_at' => $this->created_at->diffForHumans(), // Untuk mendapatkan 'Just Now'/'5m ago'
            // Kita tidak mengirim tagColor karena itu adalah logic UI front-end
            // [BARU] Kirim Nama Creator
            'creator' => $this->whenLoaded('user', function () {
                return $this->user->name;
            }),
            // [REVISI] Mengirim created_at sebagai string ISO 8601 (agar front-end Next.js dapat menghitung "time ago")
            'createdAt' => $this->created_at->toISOString(),
        ];
    }
}
