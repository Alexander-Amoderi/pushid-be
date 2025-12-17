<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLobbyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            // [VALIDASI] Judul & Kontak wajib diisi
            'game_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rank' => ['required', 'string', 'max:100'],
            'link' => ['required', 'url', 'max:255'],
            //'user_id' => ['required', 'integer', 'exists:users,id'], // Pastikan user_id valid
        ];
    }

    // Opsional: Custom message untuk validasi
    public function messages(): array
    {
        return [
            'title.required' => 'Judul Mabar wajib diisi.',
            'link.required' => 'Link WhatsApp/Discord wajib diisi.',
            'link.url' => 'Format link harus berupa URL yang valid (misal: https://wa.me/...).'
        ];
    }
}
